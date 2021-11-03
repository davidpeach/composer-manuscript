<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Playgrounds;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InitCommand extends BaseCommand
{
    protected static $defaultName = 'init';

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'dir',
                shortcut: 'd',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->setHelp(help: 'TODO')
            ->setDescription(description: 'TODO');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem;

        if ($fs->exists(files: $this->root . '/' . Playgrounds::PLAYGROUND_DIRECTORY)) {
            $this->io->warning(message: ['Playgrounds directory already exists. No action taken.']);
        } else {
            $fs->mkdir(dirs: $this->root . '/' . Playgrounds::PLAYGROUND_DIRECTORY);
            $this->io->success(message: ['Playgrounds directory created.']);
        }

        if ($fs->exists(files: $this->root . '/packages')) {
            $this->io->warning(message: ['Packages directory already exists. No action taken.']);
        } else {
            $fs->mkdir(dirs: $this->root . '/packages');
            $this->io->success(message: ['Packages directory created.']);
        }

        if ($this->config->exists()) {
            $this->io->warning(message: ['The .manuscript file already exists. No action taken']);
        } else {
            $this->config->init();
        }

        return Command::SUCCESS;
    }
}
