<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use DavidPeach\Manuscript\Config;
use DavidPeach\Manuscript\GitCredentials;
use DavidPeach\Manuscript\GithubPackageFromTemplate;
use DavidPeach\Manuscript\GithubRepository;
use DavidPeach\Manuscript\Feedback;
use Symfony\Component\Process\Process;
use Throwable;
use Exception;

class SpatiePackageBuilder implements PackageBuilderContract
{
    const TEMPLATE_OWNER = 'spatie';

    const TEMPLATE_REPOSITORY = 'package-skeleton-laravel';

    static int $attempts = 0;

    public function __construct(
        private string   $root,
        private Feedback $feedback,
        private Config   $config
    ){}

    public function build(): string
    {
        $token = $this->config->gitPersonalAccessToken() ?? $this->askForToken();

        $newGithubPackage = new GithubPackageFromTemplate(token: $token);

        try {
            $newGithubPackage->validateToken();
        } catch(Throwable) {
            if (self::$attempts > 2) {
                throw new Exception(message: "Failed 3 times to validate your Github Personal Access Token.");
            }

            $token = $this->askForToken();
            $this->config->updateConfig(key: 'git_personal_access_token', value: $token);

            self::$attempts += 1;

            return $this->build();
        }

        $guessedNamespace = (new GitCredentials)->guessNamespace();

        $question = vsprintf(
            format: 'Please enter your GitHub username / package namespace [%s]',
            values: [$guessedNamespace],
        );
        $namespace = $this->feedback->ask(question: $question, defaultAnswer: $guessedNamespace);

        $repositoryName = $this->feedback->ask(
            question: 'Please enter the name of your new repository [my-new-repository]',
            defaultAnswer: 'my-new-repository'
        );

        $newGithubPackage
            ->setTemplateOwner(templateOwner: self::TEMPLATE_OWNER)
            ->setTemplateRepository(templateRepository: self::TEMPLATE_REPOSITORY)
            ->setNamespace(namespace: $namespace)
            ->setNewRepositoryName(repo: $repositoryName);

        try {
            $newRepository = $newGithubPackage->createRepository();
        } catch (Throwable $e) {
            throw new Exception(
                message: 'Error creating repository in Github. Original exception:' . $e->getMessage()
            );
        }

        $githubRepository = (new GithubRepository)
            ->setRemoteUrl(remoteUrl: $newRepository['git_url'])
            ->setLocalDirectory(localDirectory: $this->root . '/' . $newRepository['name'])
            ->clone();

        if ($githubRepository->clonedSuccessfully()) {
            $path = $githubRepository->getLocalDirectory();
        } else {
            throw new Exception(message: "Error cloning repository.");
        }

        // Run the Spatie package configure script.
        $commands = [
            'cd ' . $path,
            'php configure.php',
            'cd ' . $this->root,
        ];

        $process = Process::fromShellCommandline(implode(separator: ' && ', array: $commands));
        $process->setTty(tty: true);
        $process->run();

        return $path;
    }

    private function askForToken(): string
    {
        $question = 'Please enter your GitHub personal access token';

        if (self::$attempts > 0) {
            $question .= self::$attempts . ' failed attempt(s) to validate GitHub personal access token';
        }

        $question .= ' : ';

        return $this->feedback->ask(question: $question, defaultAnswer: '');
    }
}
