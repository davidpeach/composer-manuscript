<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Finders\Playgrounds;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InitCommand extends BaseCommand
{
    protected static $defaultName = 'init';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription(description: 'Initialize a directory as a "manuscript" root.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem;

        if ($fs->exists(files: $this->root . '/' . $this->playgroundFinder->directoryToSearch())) {
            $this->io->warning(message: ['Playgrounds directory already exists. No action taken.']);
        } else {
            $fs->mkdir(dirs: $this->root . '/' . $this->playgroundFinder->directoryToSearch());
            $this->io->success(message: ['Playgrounds directory created.']);
        }

        if ($fs->exists(files: $this->root . '/' . $this->packageFinder->directoryToSearch())) {
            $this->io->warning(message: ['Packages directory already exists. No action taken.']);
        } else {
            $fs->mkdir(dirs: $this->root . '/' . $this->packageFinder->directoryToSearch());
            $this->io->success(message: ['Packages directory created.']);
        }

        if ($this->config->exists()) {
            $this->io->warning(message: ['The .manuscript file already exists. No action taken']);
        } else {
            $this->config->init();
            $this->io->success(message: ['.manuscript file created.']);
        }

        return Command::SUCCESS;
    }
}
