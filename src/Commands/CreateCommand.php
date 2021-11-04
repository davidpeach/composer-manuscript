<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\GitCredentials;
use DavidPeach\Manuscript\PackageBuilders\BasicPackageBuilder;
use DavidPeach\Manuscript\PackageBuilders\SpatiePackageBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
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
        parent::configure();

        $this
            ->addOption(
                name: 'play',
                shortcut: 'p',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Setup a framework playground immediately after the fresh package is created.',
                default: false,
            )
            ->addOption(
                name: 'laravel',
                shortcut: 'l',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Create a laravel package using Spatie\'s package skeleton',
                default: false,
            )
            ->setDescription(
                description: 'Scaffold a new composer package. Pass the "-l" or "--laravel" flag to scaffold a Laravel package using Spatie\'s package skeleton.',
            )
            ->setHelp(
                help:
                '=== BASIC PACKAGE ===' . PHP_EOL .
                'The basic package scaffolding will ask you a few questions about your package' . PHP_EOL .
                'and just pass those to the "composer init" command.' . PHP_EOL . PHP_EOL .

                '=== LARAVEL PACKAGE ===' . PHP_EOL .
                'If you pass the "-l" or "--laravel" flag, it will use Spatie\'s package skeleton to create a Laravel package.' . PHP_EOL .
                'For this, you will need to generate a Github Personal Access Token and pass that to manuscript when asked.' .
                PHP_EOL .
                'It will then create a new repository in your account using the package skeleton and clone it down '
                . PHP_EOL .
                'into your manuscript packages directory.' . PHP_EOL .
                'You will then be presented with some quick questions by the package scaffold configure script to set up your class and file names.' . PHP_EOL
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption(name: 'laravel') === null) {
            $this->io->title(message: '📦 Scaffolding a new LARAVEL composer package');
        } else {
            $this->io->title(message: '📦 Scaffolding a new BASIC composer package');
        }

        $packagesDirectory = $this->root . '/packages';

        try {
            $packagePath = match ($input->getOption(name: 'laravel')) {
                false => (new BasicPackageBuilder(
                    root: $packagesDirectory,
                    gitCredentials: new GitCredentials,
                    io: $this->io,
                ))->build(),
                null => (new SpatiePackageBuilder(
                    root: $packagesDirectory,
                    io: $this->io,
                    config: $this->config
                ))->build(),
                default => throw new LogicException('Unknown package type')
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
