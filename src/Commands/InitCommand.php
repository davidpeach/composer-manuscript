<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Config;
use DavidPeach\Manuscript\Playgrounds;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InitCommand extends BaseCommand
{
    protected static $defaultName = 'init';

    public function __construct(private Config $config)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription(description: 'Initialize a directory as a "manuscript" root.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->config->setDirectory($this->root);

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
            $this->io->success(message: ['.manuscript file created.']);
        }

        return Command::SUCCESS;
    }
}
