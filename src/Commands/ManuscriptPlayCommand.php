<?php

namespace DavidPeach\Manuscript\Commands;

use DavidPeach\Manuscript\ExistingPackage;
use DavidPeach\Manuscript\FrameworkChooser;
use DavidPeach\Manuscript\PackageInstaller;
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
        $helper = $this->getHelper('question');
        $root = ($input->getOption('package-dir') ?? getcwd()) . '/';
        $playgroundDirectory = $root . '../manuscript-playgrounds/';
        $this->writeIntro($output);

        $package = new ExistingPackage($input, $output, $helper, $root);
        $package->getData();

        $needsNewPlayground = true;

        $playgroundFinder = new PlaygroundFinder;
        $existingPlaygrounds = $playgroundFinder->discover($playgroundDirectory);

        if (!empty($existingPlaygrounds)) {
            $question = new ChoiceQuestion(
                '  Please select your framework playground, or select "none" to have a fresh one made for you.',
                array_merge([0 => 'none'], array_keys($existingPlaygrounds)),
                0
            );
            $question->setErrorMessage('Framework playground %s is invalid.');

            $answer = $helper->ask($input, $output, $question);

            if ($answer !== 'none') {
                $playground = $existingPlaygrounds[$answer];
                $needsNewPlayground = false;
            }
        }

        if ($needsNewPlayground) {
            $frameworks = new FrameworkChooser($input, $output, $helper);
            $chosenFramework = $frameworks->choose();
            $playground = PlaygroundBuilder::build($chosenFramework, $playgroundDirectory);
        }

        PackageInstaller::install($package, $playground);

        $output->writeln('');
        $output->writeln('<comment> 🥁 Installing ' . $package->getName() . ' into the playground</comment>');

        $output->writeln('');
        $output->writeln('<comment> ✅ ' . $package->getName() . ' installed</comment>');

        $this->writeSummary($output, $package->getPath(), $playground->getPath());
        return Command::SUCCESS;
    }


    private function writeIntro($output): void
    {
        $output->writeln('');
        $output->writeln(' 🎼 Manuscript — Composer package scaffolding and environment helper');
        $output->writeln('');
        $output->writeln(" 👌 Let's scaffold you a fresh composer package for you to start building.");
        $output->writeln('');
    }

    private function writeSummary($output, $packageDirectory, $playgroundDirectory): void
    {
        $packageDirectory = realpath($packageDirectory);
        $playgroundDirectory = realpath($playgroundDirectory);

        $output->writeln('');
        $output->writeln(' 🎉 <info>Setup complete!</info>');
        $output->writeln('');
        $output->writeln(' 🎼 <info>Thank You for using Manuscript.</info>');
        $output->writeln('');
        $output->writeln(' 📦 <info>Open <comment>' . $packageDirectory . '</comment> in your text editor and have fun building your package. 😀</info>');
        $output->writeln('');
        $output->writeln(' 🎮 <info>A playground has also been setup at <comment>' . $playgroundDirectory . ' </comment>.' . PHP_EOL . PHP_EOL . "    The playground has your package pre-installed (symlinked from your local package's folder)<info>");
        $output->writeln('');
        $output->writeln('    <info>Run <comment>cd ' . $playgroundDirectory . ' && php artisan serve</comment>' . PHP_EOL . '    in a separate terminal window to begin the playground environment.</info>');
        $output->writeln('');
        $output->writeln('    <info>Any changes made whilst developing your package will be immediately updated ' . PHP_EOL . '    in the playground.</info>');
        $output->writeln('');
        $output->writeln('    There is also a sample class added to your new package at <comment>src/Quote.php</comment>.');
        $output->writeln('');
        $output->writeln('    Then in the playground a route has been added to directly use that example class.');
        $output->writeln('    Head to <comment>http://localhost:8000/quote</comment> to see that example in action.' . PHP_EOL);
    }
}
