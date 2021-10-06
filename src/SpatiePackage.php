<?php

namespace DavidPeach\Manuscript;

use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Throwable;

class SpatiePackage extends Package
{
    const TEMPLATE_OWNER = 'spatie';

    const TEMPLATE_REPOSITORY = 'package-skeleton-laravel';

    static int $attempts = 0;

    public function getData(): self
    {
        return $this;
    }

    public function scaffold(): self
    {
        $config = (new Config(
            Helpers::determineHomeDirectory(),
            new Filesystem())
        );

        $token = $config->gitPersonalAccessToken() ?? $this->askForToken();

        $newGithubPackage = new GithubPackageFromTemplate($token);

        try {
            $newGithubPackage->validateToken();
        } catch(Throwable $e) {
            if (self::$attempts > 2) {
                throw new Exception("Failed 3 times to validate your Github Personal Access Token.");
            }

            $token = $this->askForToken();
            $config->updateConfig('git_personal_access_token', $token);

            self::$attempts += 1;

            return $this->scaffold();
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
            ->setLocalDirectory($this->directory . '/' . $newRepository['name'])
            ->clone();

        if ($githubRepository->clonedSuccessfully()) {
            $this->setPath($githubRepository->getLocalDirectory());
        } else {
            throw new Exception("Error cloning repository.");
        }

        // Run the Spatie package configure script.
        $commands = [
            'cd ' . $this->getPath(),
            'php configure.php',
            'cd ' . $this->directory,
        ];

        $process = Process::fromShellCommandline(implode(' && ', $commands));
        $process->setTty(true);
        $process->run();

        return $this;
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
