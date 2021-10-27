<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use DavidPeach\Manuscript\Config;
use DavidPeach\Manuscript\GitCredentials;
use DavidPeach\Manuscript\GithubPackageFromTemplate;
use DavidPeach\Manuscript\GithubRepository;
use DavidPeach\Manuscript\QuestionAsker;
use Symfony\Component\Process\Process;
use Throwable;
use Exception;

class SpatiePackageBuilder implements PackageBuilderContract
{
    const TEMPLATE_OWNER = 'spatie';

    const TEMPLATE_REPOSITORY = 'package-skeleton-laravel';

    static int $attempts = 0;

    public function __construct(
        private string $root,
        private QuestionAsker $questions,
        private Config $config
    ){}

    public function build(): string
    {
        $token = $this->config->gitPersonalAccessToken() ?? $this->askForToken();

        $newGithubPackage = new GithubPackageFromTemplate($token);

        try {
            $newGithubPackage->validateToken();
        } catch(Throwable $e) {
            if (self::$attempts > 2) {
                throw new Exception("Failed 3 times to validate your Github Personal Access Token.");
            }

            $token = $this->askForToken();
            $this->config->updateConfig('git_personal_access_token', $token);

            self::$attempts += 1;

            return $this->build();
        }

        $guessedNamespace = (new GitCredentials)->guessNamespace('your-namespace');

        $question = sprintf(
            ' <question> Please enter your GitHub username / package namespace [%s] </question> : ',
            $guessedNamespace,
        );
        $namespace = $this->questions->question($question)->defaultAnswer($guessedNamespace)->ask();

        $repositoryName = $this->questions->question(
            ' <question> Please enter the name of your new repository [my-new-repository] </question> : '
        )->defaultAnswer('my-new-repository')->ask();

        $newGithubPackage
            ->setTemplateOwner(self::TEMPLATE_OWNER)
            ->setTemplateRepository(self::TEMPLATE_REPOSITORY)
            ->setNamespace($namespace)
            ->setNewRepositoryName($repositoryName);

        try {
            $newRepository = $newGithubPackage->createRepository();
        } catch (Throwable $e) {
            throw new Exception(
                'Error creating repository in Github. Original exception:' . $e->getMessage()
            );
        }

        $githubRepository = (new GithubRepository)
            ->setRemoteUrl($newRepository['git_url'])
            ->setLocalDirectory($this->root . '/' . $newRepository['name'])
            ->clone();

        if ($githubRepository->clonedSuccessfully()) {
            $path = $githubRepository->getLocalDirectory();
        } else {
            throw new Exception("Error cloning repository.");
        }

        // Run the Spatie package configure script.
        $commands = [
            'cd ' . $path,
            'php configure.php',
            'cd ' . $this->root,
        ];

        $process = Process::fromShellCommandline(implode(' && ', $commands));
        $process->setTty(true);
        $process->run();

        return $path;
    }

    private function askForToken(): string
    {
        $question = ' <question> Please enter your GitHub personal access token</question>';

        if (self::$attempts > 0) {
            $question .= ' <comment> ' . self::$attempts . ' failed attempt(s) to validate GitHub personal access token </comment>';
        }

        $question .= ' : ';

        return $this->questions->question($question)->defaultAnswer('')->ask();
    }
}
