<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\GitCredentials;
use DavidPeach\Manuscript\PackageBuilders\BasicPackageBuilder;
use DavidPeach\Manuscript\PackageBuilders\SpatiePackageBuilder;
use DavidPeach\Manuscript\QuestionAsker;
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
        $root = $input->getOption('install-dir') ?? getcwd(). '/packages';

        $this->intro($output);

        $questionAsker = new QuestionAsker($input, $output, $this->getHelper('question'));

        try {
            $packagePath = match ($input->getOption('type')) {
                null => (new BasicPackageBuilder($root, $questionAsker, new GitCredentials))->build(),
                'spatie' => (new SpatiePackageBuilder($root, $questionAsker))->build(),
            };
        } catch (Throwable $e) {
            $output->writeln(' <error> ' . $e->getMessage() . ' </error>');
            return Command::FAILURE;
        }

        $this->outro($output, $packagePath);

        if ($input->getOption('play') === null) {
            try {
                $command = $this->getApplication()->find('play');
                $command->run(
                    new ArrayInput(['--package-dir'  => $packagePath,]),
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

    private function outro($output, string $packagePath): void
    {
        $output->writeln('');
        $output->writeln(' 🎉 <info>Fresh package setup complete!</info>');
        $output->writeln('');
        $output->writeln(' 🎼 <info>Thank You for using Manuscript.</info>');
        $output->writeln('');
        $output->writeln(' @ <info> Your new package is set up at <comment>' . $packagePath . ' </comment></info>');
        $output->writeln('');
    }
}
