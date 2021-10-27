<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Config;
use DavidPeach\Manuscript\PlaygroundFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InitCommand extends Command
{
    protected static $defaultName = 'init';

    protected function configure(): void
    {
        $this
            ->addOption(
                'install-dir',
                'i',
                InputOption::VALUE_OPTIONAL,
                'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->setHelp('TODO')
            ->setDescription('TODO');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = ($input->getOption('install-dir') ?? getcwd());

        $fs = new Filesystem;

        if ($fs->exists($root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY)) {
            $output->writeln('Playgrounds directory already exists. No action taken.');
        } else {
            $fs->mkdir($root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY);
            $output->writeln('Playgrounds directory created.');
        }

        if ($fs->exists($root . '/packages')) {
            $output->writeln('Packages directory already exists. No action taken.');
        } else {
            $fs->mkdir($root . '/packages');
            $output->writeln('Packages directory created.');
        }

        new Config($root, $fs);

        return Command::SUCCESS;
    }
}
