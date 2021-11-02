<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Feedback;
use DavidPeach\Manuscript\Playgrounds;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ClearPlaygroundsCommand extends Command
{
    protected static $defaultName = 'clear-playgrounds';

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'install-dir',
                shortcut: 'i',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->setHelp(help: 'This command will delete all framework playgrounds within the directory.')
            ->setDescription(description: 'Whether you pass the install-dir option, or default to the current directory, this command will look for a directory called "manuscript-playgrounds", and empty it out.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = ($input->getOption(name: 'install-dir') ?? getcwd());

        $feedback = new Feedback(input: $input, output: $output);

        $fs = new Filesystem;

        if (! $fs->exists(files: $root . '/.manuscript')) {
            $feedback->print(lines: ['Not a manuscript directory. No action taken.']);
            return Command::INVALID;
        }

        if (! $fs->exists(files: $root . '/' . Playgrounds::PLAYGROUND_DIRECTORY)) {
            $feedback->print(lines: ['Manuscript Playgrounds directory not found. No action taken.']);
            return Command::INVALID;
        }

        $playgrounds = (new Playgrounds)->discover(root: $root);

        foreach ($playgrounds as $playground) {
            $fs->remove(files: $playground->getPath());
            $feedback->print(lines: [$playground->getFolderName() . ' removed.']);
        }

        $feedback->print(lines: ['All framework playgrounds removed.']);

        return Command::SUCCESS;
    }
}
