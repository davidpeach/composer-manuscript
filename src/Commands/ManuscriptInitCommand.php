<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\FreshPackage;
use DavidPeach\Manuscript\Package;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class ManuscriptInitCommand extends Command
{
    protected static $defaultName = 'init';

    protected function configure(): void
    {
        $this
            ->addOption(
                'install-dir',
                null,
                InputOption::VALUE_OPTIONAL,
                'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->setHelp('This command will enable you to easily scaffold a composer package and have a playground in which to test your package as you build it.')
            ->setDescription('Setup a composer package development environment. Either with a freshly-scaffolded package (the default) or for an existing package in development.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = ($input->getOption('install-dir') ?? getcwd()) . '/';

        $this->intro($output);

        try {
            $package = (new FreshPackage(
                $input,
                $output,
                $this->getHelper('question'),
                $directory
            ));
            $package->scaffold();
        } catch (Throwable $e) {
            $output->writeln(' <error> ' . $e->getMessage() . ' </error>');
            return Command::FAILURE;
        }

        $this->outro($output, $package);

        return Command::SUCCESS;
    }

    private function intro($output): void
    {
        $output->writeln('');
        $output->writeln(' 🎼 Manuscript — Composer package scaffolding and environment helper');
        $output->writeln('');
        $output->writeln(" 👌 Let's scaffold you a fresh composer package for you to start building.");
        $output->writeln('');
    }

    private function outro($output, Package $package): void
    {
        $output->writeln('');
        $output->writeln(' 🎉 <info>Fresh package setup complete!</info>');
        $output->writeln('');
        $output->writeln(' 🎼 <info>Thank You for using Manuscript.</info>');
        $output->writeln('');
        $output->writeln(' @ <info> Your new package is set up at <comment>' . $package->getPath() . ' </comment></info>');
        $output->writeln('');
    }
}
