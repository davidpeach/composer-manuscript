<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\Config;
use DavidPeach\Manuscript\GitCredentials;
use DavidPeach\Manuscript\PackageBuilders\BasicPackageBuilder;
use DavidPeach\Manuscript\PackageBuilders\SpatiePackageBuilder;
use DavidPeach\Manuscript\Feedback;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

class CreateCommand extends Command
{
    protected static $defaultName = 'create';

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'play',
                shortcut: 'p',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Setup a framework playground immediately after the fresh package is created.',
                default: false
            )
            ->addOption(
                name: 'type',
                shortcut: 't',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Create a new laravel package using Spatie\'s excellent skeleton.',
            )
            ->addOption(
                name: 'install-dir',
                shortcut: 'i',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->setHelp(help: 'This command will enable you to easily scaffold a composer package and have a playground in which to test your package as you build it.')
            ->setDescription(description: 'Setup a composer package development environment. Either with a freshly-scaffolded package (the default) or for an existing package in development.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = $input->getOption(name: 'install-dir') ?? getcwd();

        $feedback = new Feedback(input: $input, output: $output);

        $fs = new Filesystem;

        if (! $fs->exists(files: $root . '/.manuscript')) {
            $output->writeln(messages: 'Not a manuscript directory. No action taken.');
            return Command::INVALID;
        }

        $packagesDirectory = $root . '/packages';

        $this->intro(output: $output);

        $config = (new Config(directory: $root, filesystem: new Filesystem));

        try {
            $packagePath = match ($input->getOption(name: 'type')) {
                null => (new BasicPackageBuilder(
                    root: $packagesDirectory,
                    feedback: $feedback,
                    gitCredentials: new GitCredentials
                ))->build(),
                'spatie' => (new SpatiePackageBuilder(
                    root: $packagesDirectory,
                    feedback: $feedback,
                    config: $config
                ))->build(),
            };
        } catch (Throwable $e) {
            $output->writeln(messages: $e->getMessage());
            return Command::FAILURE;
        }

        $this->outro(output: $output, packagePath: $packagePath);

        if ($input->getOption(name: 'play') === null) {
            try {
                $command = $this->getApplication()->find(name: 'play');
                $command->run(
                    new ArrayInput(parameters: ['--package-dir'  => $packagePath]),
                    output: $output
                );
            } catch (Throwable) {
                $output->writeln(messages: 'Apologies, but there seems to be an error with the playground setup.');
            }
        }

        return Command::SUCCESS;
    }

    private function intro(OutputInterface $output): void
    {
        $output->writeln([
            '🎼 Manuscript — Composer package scaffolding and environment helper',
            '👌 Let\'s scaffold you a fresh composer package for you to start building.',
        ]);
    }

    private function outro(OutputInterface $output, string $packagePath): void
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
