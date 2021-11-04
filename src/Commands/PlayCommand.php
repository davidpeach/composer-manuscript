<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\Exceptions\PackageInstallFailedException;
use DavidPeach\Manuscript\Exceptions\PackageModelNotCreatedException;
use DavidPeach\Manuscript\FrameworkChooser;
use DavidPeach\Manuscript\PackageBuilders\PlaygroundPackageBuilder;
use DavidPeach\Manuscript\PackageInstaller;
use DavidPeach\Manuscript\PackageModel;
use DavidPeach\Manuscript\PackageModelFactory;
use DavidPeach\Manuscript\Playgrounds;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PlayCommand extends BaseCommand
{
    protected static $defaultName = 'play';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription(
                description: 'When ran from inside a package (that lives inside the manuscript packages directory), it will install that package into a local framework playground.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $package = (new PackageModelFactory(composer: new ComposerFileManager))
                ->fromPath(pathToPackage: $this->root);
        } catch (PackageModelNotCreatedException) {
            $this->io->error(message: ['Not a valid composer package. No action taken.']);
            return Command::INVALID;
        }

        $this->io->title(message: '🎪 Setting up a playground for your package: ' . $package->getName());

        $root = $this->root . '/../..';

        $fs = new Filesystem;

        if (!$fs->exists(files: $root . '/.manuscript')) {
            $this->io->error(message: ['Not a manuscript directory. No action taken.']);
            return Command::INVALID;
        }

        // Should this create?
        if (!$fs->exists(files: $root . '/playgrounds')) {
            $fs->mkdir(dirs: $root . '/playgrounds');
        }

        try {
            $playground = $this->getPlayground(root: $root);
        } catch (PackageModelNotCreatedException) {
            $this->io->error(message: ['Error setting up a package playground']);
            return Command::FAILURE;
        }

        try {
            (new PackageInstaller(composer: new ComposerFileManager))->install(
                package: $package,
                playground: $playground
            );
        } catch (PackageInstallFailedException | ProcessFailedException) {
            $this->io->error(message: ['Error installing the package into the playground']);
            return Command::FAILURE;
        }

        $this->io->success(message: [
            '🎪 Playground setup complete!',
            '🎼 Thank You for using Manuscript.',
            'Your package has been installed into the playground at ' . realpath($playground->getPath()),
            'Any changes made to your package whilst developing it will be updated in the playground automatically.',
        ]);

        return Command::SUCCESS;
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

            $answer = $this->io->choice(
                question: 'Please select your framework playground, or select "new" to have a fresh one made for you.',
                choices: array_merge([0 => 'new'], array_keys($existingPlaygrounds)),
                default: 0
            );

            if ($answer !== 'new') {
                $playground = $existingPlaygrounds[$answer];
            }
        }

        if (is_null($playground)) {

            $frameworks = new FrameworkChooser(io: $this->io);

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
