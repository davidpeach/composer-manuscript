<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\PlaygroundFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ManuscriptStatusCommand extends Command
{
    protected static $defaultName = 'status';

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

        $playgrounds = (new PlaygroundFinder(new ComposerFileManager))->discover($root);
        $tableRows = [];

        foreach ($playgrounds as $playground) {
            $tableRows[] = [
                $playground->getName()
            ];
        }

        $output->writeln('Playgrounds');
        $table = new Table($output);
        $table
            ->setHeaders(['Title'])
            ->setRows($tableRows)
        ;
        $table->render();

        return Command::SUCCESS;
    }
}
