<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use DavidPeach\Manuscript\Config;
use DavidPeach\Manuscript\GitCredentials;
use DavidPeach\Manuscript\GithubPackageFromTemplate;
use DavidPeach\Manuscript\GithubRepository;
use Symfony\Component\Console\Style\StyleInterface;
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
        private StyleInterface $io,
        private Config   $config
    )
    {
    }

    /**
     * @return string
     * @throws Exception
     */
    public function build(): string
    {
        $token = $this->config->gitPersonalAccessToken() ?? $this->askForToken();

        $newGithubPackage = new GithubPackageFromTemplate(token: $token);

        try {
            $newGithubPackage->validateToken();
        } catch (Throwable) {
            if (self::$attempts > 2) {
                throw new Exception(message: "Failed 3 times to validate your Github Personal Access Token.");
            }

            $token = $this->askForToken();
            $this->config->update(key: 'git_personal_access_token', value: $token);

            self::$attempts += 1;

            return $this->build();
        }


        // validate
        $namespace = $this->io->ask(
            question: 'Please enter your GitHub username / package namespace',
            default: (new GitCredentials)->guessNamespace()
        );

        // validate
        $repositoryName = $this->io->ask(
            question: 'Please enter the name of your new repository',
            default: 'my-new-repository'
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

    /**
     * @return string
     */
    private function askForToken(): string
    {
        if (self::$attempts > 0) {
            $this->io->warning(message: self::$attempts . ' failed attempt(s) to validate GitHub personal access token');
        }

        return $this->io->ask(question: 'Please enter your GitHub personal access token', default: '');
    }
}
