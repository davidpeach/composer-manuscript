<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Config;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class BaseCommand extends Command
{
    protected string $root;

    protected StyleInterface $io;

    protected Config $config;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->root = $input->getOption(name: 'dir') ?? getcwd();

        if ($this->shouldBlock(directory: $this->root)) {
            throw new LogicException(message: '🔌 Not a manuscript directory. No action taken.');
        }

        $this->io = new SymfonyStyle(input: $input, output: $output);

        $this->config = (new Config(directory: $this->root, filesystem: new Filesystem));
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
