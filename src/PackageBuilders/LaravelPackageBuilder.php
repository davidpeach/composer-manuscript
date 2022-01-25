<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Process\Process;

class LaravelPackageBuilder implements PackageBuilderContract
{
    const TEMPLATE_REPO = 'https://github.com/davidpeach/composer-manuscript-package-template-laravel.git';
    private string $root;

    private $io;

    /**
     * @inheritDoc
     */
    public function build(): string
    {
        $packageName = $this->io->ask('What would you like to call your new package?');
        $packageName = str_replace(search: [' '], replace: '-', subject: $packageName);

        // Download the template from Github
        $process = new Process(command: [
            'git',
            'clone',
            self::TEMPLATE_REPO,
            $packageName
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

        $process->setWorkingDirectory($this->root . '/' . $packageName);
        $process->setTimeout(timeout: 3600);
        $process->run();

        if (! $process->isSuccessful()) {
            // throw error
        }

        $process = new Process(command: [
            'git',
            'init',
        ]);

        $process->setWorkingDirectory($this->root . '/' . $packageName);
        $process->setTimeout(timeout: 3600);
        $process->run();

        if (! $process->isSuccessful()) {
            // throw error
        }

        // Ask questions about the package to build it up.

        return $this->root . '/' . $packageName;
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