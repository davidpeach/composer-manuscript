<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\DevPackageModel;
use DavidPeach\Manuscript\Finders\DevPackages;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListPackagesCommand extends BaseCommand
{
    protected static $defaultName = 'list:packages';

    public function __construct(
        private DevPackages $devPackages,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription(description: 'List local packages in development');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $packages =$this->devPackages->discover($this->root);

        $packages = array_map(function (DevPackageModel $package) {
            return [ $package->getName(), $package->getCurrentBranch() ];
        }, $packages);

        $table = new Table($output);
        $table
            ->setHeaders(['Package Name', 'Current branch'])
            ->setRows($packages)
        ;
        $table->render();

        return Command::SUCCESS;
    }
}
