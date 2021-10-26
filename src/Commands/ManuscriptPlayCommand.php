<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\ExistingPackage;
use DavidPeach\Manuscript\FrameworkChooser;
use DavidPeach\Manuscript\Package;
use DavidPeach\Manuscript\PackageInstaller;
use DavidPeach\Manuscript\Playground\Playground;
use DavidPeach\Manuscript\Playground\PlaygroundBuilder;
use DavidPeach\Manuscript\Playground\PlaygroundFinder;
use DavidPeach\Manuscript\QuestionAsker;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ManuscriptPlayCommand extends Command
{
    protected static $defaultName = 'play';

    protected function configure(): void
    {
        $this
            ->addOption(
                'package-dir',
                null,
                InputOption::VALUE_OPTIONAL,
                'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->setHelp('This command will enable you to easily scaffold a composer package and have a playground in which to test your package as you build it.')
            ->setDescription('Setup a composer package development environment. Either with a freshly-scaffolded package (the default) or for an existing package in development.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $root = ($input->getOption('package-dir') ?? getcwd());

        $this->intro($output);

        $fs = new Filesystem;

        if (! $fs->exists($root . '/../playgrounds/')) {
            $fs->mkdir($root . '/../playgrounds/');
        }

        $questionAsker = new QuestionAsker(
            $input,
            $output,
            $this->getHelper('question')
        );

        $package = new ExistingPackage(
            $root,
            $questionAsker,
            new ComposerFileManager
        );
        $package->setPath($root);
        $package->getData();


        $playground = $this->getPlayground(
            $root . '/../',
            $input,
            $output,
            $package
        );

        (new PackageInstaller(new ComposerFileManager))->install(
            $package,
            $playground
        );

        $this->outro($output, $playground);

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

    private function outro($output, Playground $playground): void
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Package $package
     * @return Playground
     */
    protected function getPlayground(
        string          $root,
        InputInterface  $input,
        OutputInterface $output,
        Package $package
    ): Playground
    {
        $playground = null;

        $existingPlaygrounds = (new PlaygroundFinder(new ComposerFileManager))->discover($root);

        if (!empty($existingPlaygrounds)) {

            $question = new ChoiceQuestion(
                '  Please select your framework playground, or select "new" to have a fresh one made for you.',
                array_merge([0 => 'new'], array_keys($existingPlaygrounds)),
                0
            );
            $question->setErrorMessage('Framework playground %s is invalid.');

            $answer = $this->getHelper('question')->ask($input, $output, $question);

            if ($answer !== 'new') {
                $playground = $existingPlaygrounds[$answer];
            }
        }

        if (is_null($playground)) {
            $frameworks = new FrameworkChooser(
                $input,
                $output,
                $this->getHelper('question')
            );

            $playground = (new PlaygroundBuilder)->forPackage($package)->build(
                $frameworks->choose(),
                $root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY
            );
        }

        return $playground;
    }
}
