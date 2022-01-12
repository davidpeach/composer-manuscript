<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Finders\PlaygroundPackages;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ClearPlaygroundsCommand extends BaseCommand
{
    protected static $defaultName = 'clear-playgrounds';

    public function __construct(private PlaygroundPackages $playgrounds)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription(
                description: 'Clear out the playgrounds directory.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem;

        if (! $fs->exists(files: $this->root . '/' . $this->playgrounds->directoryToSearch())) {
            $this->io->error(message: ['Manuscript Playgrounds directory not found. No action taken.']);
            return Command::INVALID;
        }

        $playgrounds = $this->playgrounds->discover(root: $this->root);

        foreach ($playgrounds as $playground) {
            $fs->remove(files: $playground->getPath());
            $this->io->info(message: [$playground->getFolderName() . ' removed.']);
        }

        $this->io->success(message: ['All framework playgrounds removed.']);

        return Command::SUCCESS;
    }
}
