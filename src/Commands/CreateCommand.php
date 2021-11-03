<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\GitCredentials;
use DavidPeach\Manuscript\PackageBuilders\BasicPackageBuilder;
use DavidPeach\Manuscript\PackageBuilders\SpatiePackageBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class CreateCommand extends BaseCommand
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
                name: 'dir',
                shortcut: 'd',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->setHelp(help: 'This command will enable you to easily scaffold a composer package and have a playground in which to test your package as you build it.')
            ->setDescription(description: 'Setup a composer package development environment. Either with a freshly-scaffolded package (the default) or for an existing package in development.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title(message: '📦 Scaffolding a new composer package');

        $packagesDirectory = $this->root . '/packages';

        try {
            $packagePath = match ($input->getOption(name: 'type')) {
                null => (new BasicPackageBuilder(
                    root: $packagesDirectory,
                    gitCredentials: new GitCredentials,
                    io: $this->io,
                ))->build(),
                'spatie' => (new SpatiePackageBuilder(
                    root: $packagesDirectory,
                    io: $this->io,
                    config: $this->config
                ))->build(),
            };
        } catch (Throwable $e) {
            $this->io->error(message: $e->getMessage());
            return Command::FAILURE;
        }

        $this->io->success(message: [
            '🎉 Fresh package setup complete!',
            '🎼 Thank You for using Manuscript.',
            '📦 Your new package is set up at ' . $packagePath,
        ]);

        if ($input->getOption(name: 'play') === null) {
            try {
                $command = $this->getApplication()->find(name: 'play');
                $command->run(
                    input: new ArrayInput(parameters: ['--dir' => $packagePath]),
                    output: $output
                );
            } catch (Throwable) {
                $this->io->warning(message: [
                    'Apologies, but there seems to be an error with the playground setup.',
                    'Your new package is still setup though.'
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
