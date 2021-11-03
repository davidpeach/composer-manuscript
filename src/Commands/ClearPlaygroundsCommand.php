<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Playgrounds;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ClearPlaygroundsCommand extends BaseCommand
{
    protected static $defaultName = 'clear-playgrounds';

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'dir',
                shortcut: 'd',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->setHelp(help: 'This command will delete all framework playgrounds within the directory.')
            ->setDescription(description: 'Whether you pass the install-dir option, or default to the current directory, this command will look for a directory called "manuscript-playgrounds", and empty it out.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem;

        if (! $fs->exists(files: $this->root . '/' . Playgrounds::PLAYGROUND_DIRECTORY)) {
            $this->io->error(message: ['Manuscript Playgrounds directory not found. No action taken.']);
            return Command::INVALID;
        }

        $playgrounds = (new Playgrounds)->discover(root: $this->root);

        foreach ($playgrounds as $playground) {
            $fs->remove(files: $playground->getPath());
            $this->io->info(message: [$playground->getFolderName() . ' removed.']);
        }

        $this->io->success(message: ['All framework playgrounds removed.']);

        return Command::SUCCESS;
    }
}
