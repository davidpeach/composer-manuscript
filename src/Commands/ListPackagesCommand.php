<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\PackageModel;
use DavidPeach\Manuscript\Packages;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
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
        $packages = (new Packages())->discover($this->root);

        $packages = array_map(function (PackageModel $package) {
            return [ $package->getName() ];
        }, $packages);

        $table = new Table($output);
        $table
            ->setHeaders(['Package Name'])
            ->setRows($packages)
        ;
        $table->render();

        return Command::SUCCESS;
    }
}
