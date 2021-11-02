<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\Exceptions\PackageInstallFailedException;
use DavidPeach\Manuscript\Exceptions\PackageModelNotCreatedException;
use DavidPeach\Manuscript\Feedback;
use DavidPeach\Manuscript\FrameworkChooser;
use DavidPeach\Manuscript\PackageBuilders\PlaygroundPackageBuilder;
use DavidPeach\Manuscript\PackageInstaller;
use DavidPeach\Manuscript\PackageModel;
use DavidPeach\Manuscript\PackageModelFactory;
use DavidPeach\Manuscript\Playgrounds;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PlayCommand extends Command
{
    protected static $defaultName = 'play';

    private Feedback $feedback;

    protected function configure(): void
    {
        $this
            ->addOption(
                name: 'package-dir',
                shortcut: 'p',
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
        $packageDirectory = ($input->getOption(name: 'package-dir') ?? getcwd());

        $this->feedback = new Feedback(input: $input, output: $output);

        try {
            $package = (new PackageModelFactory(composer: new ComposerFileManager))
                ->fromPath(pathToPackage: $packageDirectory);
        } catch (PackageModelNotCreatedException) {
            $this->feedback->print(lines: ['Not a valid composer package. No action taken.']);
            return Command::INVALID;
        }

        $root = $packageDirectory . '/../..';

        $fs = new Filesystem;

        if (!$fs->exists(files: $root . '/.manuscript')) {
            $this->feedback->print(lines: ['Not a manuscript directory. No action taken.']);
            return Command::INVALID;
        }

        $this->intro(output: $output);

        if (!$fs->exists(files: $root . '/playgrounds')) {
            $fs->mkdir(dirs: $root . '/playgrounds');
        }

        try {
            $playground = $this->getPlayground(root: $root);
        } catch (PackageModelNotCreatedException) {
            $this->feedback->print(lines: ['Error setting up a package playground']);
            return Command::FAILURE;
        }

        try {
            (new PackageInstaller(composer: new ComposerFileManager))->install(
                package: $package,
                playground: $playground
            );
        } catch (PackageInstallFailedException | ProcessFailedException) {
            $this->feedback->print(lines: ['Error installing the package into the playground']);
            return Command::FAILURE;
        }

        $this->outro(output: $output, playground: $playground);

        return Command::SUCCESS;
    }


    private function intro(OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln(' 🎼 Manuscript — Composer package scaffolding and environment helper');
        $output->writeln('');
        $output->writeln(" 👌 Let's scaffold you a fresh composer package for you to start building.");
        $output->writeln('');
    }

    private function outro(OutputInterface $output, PackageModel $playground): void
    {
        $output->writeln('');
        $output->writeln(' 🎮 <info>Playground setup complete!</info>');
        $output->writeln('');
        $output->writeln(' 🎼 <info>Thank You for using Manuscript.</info>');
        $output->writeln('');
        $output->writeln('    <info>Your package has been installed into the playground at <comment>' .
            realpath($playground->getPath()) . '</comment>.</info>');
        $output->writeln('    <info>Any changes made to your package whilst developing it will be updated in the playground automatically.</info>');
        $output->writeln('');
    }

    /**
     * @param string $root
     * @return PackageModel
     * @throws PackageModelNotCreatedException
     */
    protected function getPlayground(string $root): PackageModel
    {
        $playground = null;

        $existingPlaygrounds = (new Playgrounds)->discover(root: $root);

        if (!empty($existingPlaygrounds)) {

            $answer = $this->feedback->choose(
                question: 'Please select your framework playground, or select "new" to have a fresh one made for you.',
                choices: array_merge([0 => 'new'], array_keys($existingPlaygrounds)),
                defaultKey: 0
            );

            if ($answer !== 'new') {
                $playground = $existingPlaygrounds[$answer];
            }
        }

        if (is_null($playground)) {

            $frameworks = new FrameworkChooser(feedback: $this->feedback);

            $chosenFramework = $frameworks->choose();

            $pathToPlayground = (new PlaygroundPackageBuilder(
                root: $root . '/' . Playgrounds::PLAYGROUND_DIRECTORY,
                framework: $chosenFramework
            ))->build();

            return (new PackageModelFactory(composer: new ComposerFileManager))
                ->fromPath(pathToPackage: $pathToPlayground);
        }

        return $playground;
    }
}
