<?php

namespace DavidPeach\Manuscript\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListPackagesCommand extends BaseCommand
{
    protected static $defaultName = 'list:packages';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription(description: 'List local packages in development');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->warning(message: 'No functionality yet!');

        return Command::SUCCESS;
    }
}
