<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use DavidPeach\Manuscript\Github\GithubRepository;
use DavidPeach\Manuscript\Utilities\GitCredentials;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Process\Process;

class LaravelPackageBuilder implements PackageBuilderContract
{
    const TEMPLATE_REPO = 'https://github.com/davidpeach/composer-manuscript-package-template-laravel.git';

    private string $root;

    private StyleInterface $io;

    private string $package;

    public function __construct(
        private GitCredentials $git,
        private GithubRepository $githubRepository,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function build(): string
    {
        $this->package = $this->io->ask(question: 'What would you like to call your new package?');
        $this->package = str_replace(search: [' '], replace: '-', subject: $this->package);

        $this->githubRepository
            ->setWorkingDirectory(workingDirectory: $this->root)
            ->setLocalDirectory(localDirectory: $this->root . '/' . $this->package)
            ->setRemoteUrl(remoteUrl: self::TEMPLATE_REPO)
            ->clone();


        $process = new Process(command: [
            'rm',
            '-rf',
            '.git'
        ]);

        $process->setWorkingDirectory($this->root . '/' . $this->package);
        $process->setTimeout(timeout: 3600);
        $process->run();

        if (! $process->isSuccessful()) {
            // throw error
        }

        $process = new Process(command: [
            'git',
            'init',
        ]);

        $process->setWorkingDirectory($this->root . '/' . $this->package);
        $process->setTimeout(timeout: 3600);
        $process->run();

        if (! $process->isSuccessful()) {
            // throw error
        }



        $vendor = $this->io->ask(
            question: 'Please enter your GitHub username / package namespace',
            default: $this->git->guessNamespace()
        );

        $description = $this->io->ask(
            question: 'Add Description',
            default: 'Your Package Description Here'
        );

        $authorName = $this->io->ask(
            question: 'Your Name',
            default: $this->git->getName()
        );

        $authorEmail = $this->io->ask(
            question: 'Your Email',
            default: $this->git->getEmail()
        );

        $phpMinimumVersion = $this->io->ask(
            question: 'Minimum PHP Version',
            default: '8.0'
        );

        $phpNamespace = $this->io->ask(
            question: 'Your PHP Namespace',
            default: 'ChangeMe'
        );

        $composerPackageContents = file_get_contents(filename: $this->githubRepository->getLocalDirectory() . '/' . 'composer.json');
        $composerPackageContents = str_replace([
            '%vendor%',
            '%package%',
            '%description%',
            '%author_name%',
            '%author_email%',
            '%php_minimum_version%',
            '%php_namespace%'
        ], [
            $vendor,
            $this->package,
            $description,
            $authorName,
            $authorEmail,
            $phpMinimumVersion,
            $phpNamespace
        ], $composerPackageContents);

        file_put_contents(
            filename: $this->githubRepository->getLocalDirectory() . '/' . 'composer.json',
            data: $composerPackageContents
        );

        return $this->root . '/' . $this->package;
    }

    public function setRoot(string $root): self
    {
        $this->root = $root;

        return $this;
    }

    public function setIO(StyleInterface $io): self
    {
        $this->io = $io;

        return $this;
    }
}