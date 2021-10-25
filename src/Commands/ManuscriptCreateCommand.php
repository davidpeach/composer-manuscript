<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\FreshPackage;
use DavidPeach\Manuscript\Package;
use DavidPeach\Manuscript\QuestionAsker;
use DavidPeach\Manuscript\SpatiePackage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class ManuscriptCreateCommand extends Command
{
    protected static $defaultName = 'create';

    protected function configure(): void
    {
        $this
            ->addOption(
                'play',
                null,
                InputOption::VALUE_OPTIONAL,
                'Setup a framework playground immediately after the fresh package is created.',
                false
            )
            ->addOption(
                'type',
                null,
                InputOption::VALUE_OPTIONAL,
                'Create a new laravel package using Spatie\'s excellent skeleton.',
            )
            ->addOption(
                'install-dir',
                'i',
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

        $questionAsker = new QuestionAsker($input, $output, $this->getHelper('question'));

        try {
            $package = match ($input->getOption('type')) {
                null => (new FreshPackage($directory, $questionAsker, new ComposerFileManager))->getData()->scaffold()->package(),
                'spatie' => (new SpatiePackage($directory, $questionAsker, new ComposerFileManager))->scaffold()->package(),
            };
        } catch (Throwable $e) {
            $output->writeln(' <error> ' . $e->getMessage() . ' </error>');
            return Command::FAILURE;
        }

        $this->outro($output, $package);

        if ($input->getOption('play') === null) {
            try {
                $command = $this->getApplication()->find('play');
                $command->run(
                    new ArrayInput(['--package-dir'  => $package->getPath(),]),
                    $output
                );
            } catch (Throwable $e) {
                $output->writeln('Apologies, but there seems to be an error with the playground setup.');
            }
        }

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
