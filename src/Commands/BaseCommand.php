<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Config;
use DavidPeach\Manuscript\Container\Container;
use DavidPeach\Manuscript\Scratch\MyClass;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class BaseCommand extends Command
{
    protected string $root;

    protected StyleInterface $io;

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'dir',
                shortcut: 'd',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The root directory where your packages in development live. Defaults to the current directory.'
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->root = $input->getOption(name: 'dir') ?? getcwd();

        if ($this->shouldBlock(directory: $this->root)) {
            throw new LogicException(message: '🔌 Not a manuscript directory. No action taken.');
        }

        $this->io = new SymfonyStyle(input: $input, output: $output);
    }

    #[Pure] private function shouldBlock(string $directory): bool
    {
        if ($this->getName() === 'init') {
            return false;
        } elseif ($this->getName() === 'play') {
            return ! file_exists(filename: $directory . '/../../.manuscript');
        }

        return ! file_exists(filename: $directory . '/.manuscript');
    }
}
