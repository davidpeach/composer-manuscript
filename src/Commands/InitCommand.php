<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Config;
use DavidPeach\Manuscript\Feedback;
use DavidPeach\Manuscript\Playgrounds;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InitCommand extends Command
{
    protected static $defaultName = 'init';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'install-dir',
                shortcut: 'i',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->setHelp(help: 'TODO')
            ->setDescription(description: 'TODO');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = ($input->getOption(name: 'install-dir') ?? getcwd());

        $feedback = new Feedback(input: $input, output: $output);

        $fs = new Filesystem;

        if ($fs->exists(files: $root . '/' . Playgrounds::PLAYGROUND_DIRECTORY)) {
            $feedback->print(lines: ['Playgrounds directory already exists. No action taken.']);
        } else {
            $fs->mkdir(dirs: $root . '/' . Playgrounds::PLAYGROUND_DIRECTORY);
            $feedback->print(lines: ['Playgrounds directory created.']);
        }

        if ($fs->exists(files: $root . '/packages')) {
            $feedback->print(lines: ['Packages directory already exists. No action taken.']);
        } else {
            $fs->mkdir(dirs: $root . '/packages');
            $feedback->print(lines: ['Packages directory created.']);
        }

        new Config(directory: $root, filesystem: $fs);

        return Command::SUCCESS;
    }
}
