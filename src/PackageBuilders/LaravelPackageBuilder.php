<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use DavidPeach\Manuscript\Utilities\GitCredentials;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Process\Process;

class LaravelPackageBuilder implements PackageBuilderContract
{
    const TEMPLATE_REPO = 'https://github.com/davidpeach/composer-manuscript-package-template-laravel.git';

    private string $root;

    private $io;

    public function __construct(private GitCredentials $git)
    {
    }

    /**
     * @inheritDoc
     */
    public function build(): string
    {
        $package = $this->io->ask('What would you like to call your new package?');
        $package = str_replace(search: [' '], replace: '-', subject: $package);

        // Download the template from Github
        $process = new Process(command: [
            'git',
            'clone',
            self::TEMPLATE_REPO,
            $package
        ]);

        $process->setWorkingDirectory($this->root);
        $process->setTimeout(timeout: 3600);
        $process->run();

        if (! $process->isSuccessful()) {
            // throw error
        }

        // let Github do its thing
        sleep(seconds: 3);

        $process = new Process(command: [
            'rm',
            '-rf',
            '.git'
        ]);

        $process->setWorkingDirectory($this->root . '/' . $package);
        $process->setTimeout(timeout: 3600);
        $process->run();

        if (! $process->isSuccessful()) {
            // throw error
        }

        $process = new Process(command: [
            'git',
            'init',
        ]);

        $process->setWorkingDirectory($this->root . '/' . $package);
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

        $phpNamespace = 'ChangeMe';

        $composerPackageContents = file_get_contents($this->root . '/' . $package . '/' . 'composer.json');
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
            $package,
            $description,
            $authorName,
            $authorEmail,
            $phpMinimumVersion,
            $phpNamespace
        ], $composerPackageContents);

        file_put_contents(
            filename: $this->root . '/' . $package . '/' . 'composer.json',
            data: $composerPackageContents
        );

        return $this->root . '/' . $package;
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