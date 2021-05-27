<?php

namespace Davidpeach\Manuscript;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ManuscriptCommand extends Command
{
    protected static $defaultName = 'setup';

    private $isCurrent;

    protected function configure(): void
    {
        $this
            ->addOption(
                'install-dir',
                null,
                InputOption::VALUE_OPTIONAL,
                'The root directory where your packages in development live. Defaults to the current directory.'
            )
            ->addOption(
                'current',
                null,
                InputOption::VALUE_OPTIONAL,
                'The current folder is an existing package in development. (No new package will be scaffolded)',
                false
            )
            ->setHelp('This command will enable you to easily scaffold a composer package and have a playground in which to test your package as you build it.')
            ->setDescription('Setup a composer package development environment. Either with a freshly-scaffolded package (the default) or for an existing package in development.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $cwd = getcwd();

        $this->isCurrent = $this->determineIfIsCurrent($cwd, $input);
        $installDirectory = $this->determineInstallDirectory($cwd, $input);

        $this->writeIntro($output);

        if ($this->isCurrent) {
            $package = new ExistingPackage($input, $output, $helper, $installDirectory);
            $package->getData();
        } else {
            $package = new FreshPackage($input, $output, $helper, $installDirectory);
            $package->getData();
            $package->scaffold($installDirectory);
        }

        $playground = new Playground($input, $output, $helper);

        $playground->install($installDirectory);

        PackageInstaller::install(
            $playground->getDirectory(),
            $package->getDirectory(),
            $package->getName()
        );

        if ( ! $this->isCurrent) {

            $output->writeln("");
            $output->writeln("<comment> 🥁 Installing " . $package->getName() . " into the playground</comment>");

            PackageInstaller::addDemoRoute(
                $playground->getDirectory(),
                $package->getNamespace()
            );
            $output->writeln("");
            $output->writeln("<comment> ✅ " . $package->getName() . " installed</comment>");
        }

        $this->writeSummary($output, $package->getDirectory(), $playground->getDirectory());
        return Command::SUCCESS;
    }

    private function determineIfIsCurrent(string $cwd, $input)
    {
        if (! file_exists($cwd . '/composer.json')) {
            return false;
        }

        $isCurrent = $input->getOption('current');

        return $isCurrent !== false;
    }

    private function determineInstallDirectory(string $cwd, $input)
    {
        if ($this->isCurrent) {
            return $cwd . '/../';
        }

        $installDirectory = $input->getOption('install-dir');

        if (! $installDirectory) {
            return $cwd . '/';
        }

        if (! file_exists($cwd . '/' . $input->getOption('install-dir'))) {
            return $cwd . '/';
        }

        return $cwd . '/' . $input->getOption('install-dir') . '/';
    }

    private function writeIntro($output)
    {
        $output->writeln("");
        $output->writeln(" 🎼 Manuscript — Composer package scaffolding and environment helper");
        $output->writeln("");

        if ($this->isCurrent) {
            $output->writeln(" 👌 Setting up a playground and installing your existing local package into it.");
        } else {
            $output->writeln(" 👌 Let's scaffold you a fresh composer package for you to start building.");
        }

        $output->writeln("");
    }

    private function writeSummary($output, $packageDirectory, $playgroundDirectory)
    {
        $packageDirectory = realpath($packageDirectory);
        $playgroundDirectory = realpath($playgroundDirectory);

        $output->writeln("");
        $output->writeln(" 🎉 <info>Setup complete!</info>");
        $output->writeln("");
        $output->writeln(" 🎼 <info>Thank You for using Manuscript.</info>");
        $output->writeln("");
        $output->writeln(" 📦 <info>Open <comment>" . $packageDirectory . "</comment> in your text editor and have fun building your package. 😀</info>");
        $output->writeln("");
        $output->writeln(" 🎮 <info>A playground has also been setup at <comment>" . $playgroundDirectory . ' </comment>.' . PHP_EOL . PHP_EOL . "    The playground has your package pre-installed (symlinked from your local package's folder)<info>");
        $output->writeln("");
        $output->writeln("    <info>Run <comment>cd " . $playgroundDirectory . " && php artisan serve</comment>" . PHP_EOL . "    in a separate terminal window to begin the playground environment.</info>");
        $output->writeln("");
        $output->writeln("    <info>Any changes made whilst developing your package will be immediately updated " . PHP_EOL . "    in the playground.</info>");
        $output->writeln("");
        if ( ! $this->isCurrent) {
            $output->writeln("    There is also a sample class added to your new package at <comment>src/Quote.php</comment>.");
            $output->writeln("");
            $output->writeln("    Then in the playground a route has been added to directly use that example class.");
            $output->writeln("    Head to <comment>http://localhost:8000/quote</comment> to see that example in action." . PHP_EOL);
        }
    }
}
