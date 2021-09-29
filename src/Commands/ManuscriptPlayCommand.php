<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\ExistingPackage;
use DavidPeach\Manuscript\FrameworkChooser;
use DavidPeach\Manuscript\PackageInstaller;
use DavidPeach\Manuscript\Playground;
use DavidPeach\Manuscript\PlaygroundBuilder;
use DavidPeach\Manuscript\PlaygroundFinder;
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
        $root = ($input->getOption('package-dir') ?? getcwd()) . '/';

        $this->intro($output);

        $playground = $this->getPlayground(
            $root . '../manuscript-playgrounds/',
            $input,
            $output
        );

        PackageInstaller::install(
            new ExistingPackage(
                $input,
                $output,
                $this->getHelper('question'),
                $root
            ),
            $playground
        );

        $this->outro($output);

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

    private function outro($output): void
    {
        $output->writeln('');
        $output->writeln(' 🎮 <info>Playground setup complete!</info>');
        $output->writeln('');
        $output->writeln(' 🎼 <info>Thank You for using Manuscript.</info>');
        $output->writeln('');
        $output->writeln('    <info>Your package has been installed into the playground.</info>');
        $output->writeln('    <info>Any changes made to your package whilst developing it updated in the playground automatically.</info>');
    }

    /**
     * @param string $playgroundDirectory
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Playground
     */
    protected function getPlayground(
        string          $playgroundDirectory,
        InputInterface  $input,
        OutputInterface $output
    ): Playground
    {
        $playground = null;

        $existingPlaygrounds = (new PlaygroundFinder)->discover($playgroundDirectory);

        if (!empty($existingPlaygrounds)) {
            $question = new ChoiceQuestion(
                '  Please select your framework playground, or select "none" to have a fresh one made for you.',
                array_merge([0 => 'none'], array_keys($existingPlaygrounds)),
                0
            );
            $question->setErrorMessage('Framework playground %s is invalid.');

            $answer = $this->getHelper('question')->ask($input, $output, $question);

            if ($answer !== 'none') {
                $playground = $existingPlaygrounds[$answer];
            }
        }

        if (is_null($playground)) {
            $frameworks = new FrameworkChooser(
                $input,
                $output,
                $this->getHelper('question')
            );

            $playground = PlaygroundBuilder::build(
                $frameworks->choose(),
                $playgroundDirectory
            );
        }

        return $playground;
    }
}
