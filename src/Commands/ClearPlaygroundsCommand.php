<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\PlaygroundFinder;
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
                'install-dir',
                'i',
                InputOption::VALUE_OPTIONAL,
                'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->setHelp('This command will delete all framework playgrounds within the directory.')
            ->setDescription('Whether you pass the install-dir option, or default to the current directory, this command will look for a directory called "manuscript-playgrounds", and empty it out.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = ($input->getOption('install-dir') ?? getcwd());

        // check if is a manuscript root

        $fs = new Filesystem;

        if (! $fs->exists($root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY)) {
            $output->writeln('<error>Manuscript Playgrounds directory not found. No action taken.</error>');
            return Command::INVALID;
        }

        $playgrounds = (new PlaygroundFinder)->discover($root);

        foreach ($playgrounds as $playground) {
            $fs->remove($playground->getPath());
            $output->writeln('<info>' . $playground->getFolderName() . ' removed.</info>');
        }

        $output->writeln('<info>All framework playgrounds removed.</info>');

        return Command::SUCCESS;
    }
}
