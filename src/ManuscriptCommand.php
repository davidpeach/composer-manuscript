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
                'The directory to setup the environment in.'
            )
            ->addOption(
                'current',
                null,
                InputOption::VALUE_OPTIONAL,
                'If the current folder is an existing package in development',
                false
            )
            ->setHelp('This command allows you to create a composer package...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $cwd = getcwd();

        $this->isCurrent = $this->determineIfIsCurrent($cwd, $input);
        $installDirectory = $this->determineInstallDirectory($cwd, $input);

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
            PackageInstaller::addDemoRoute(
                $playground->getDirectory(),
                $package->getNamespace()
            );
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

    private function writeSummary($output, $packageDirectory, $playgroundDirectory)
    {
        $packageDirectory = realpath($packageDirectory);
        $playgroundDirectory = realpath($playgroundDirectory);

        $output->writeln("");
        $output->writeln(" ✅ <info>Setup complete!</info>");
        $output->writeln("");
        $output->writeln(" 🎼 <info>Thank You for using Manuscript.</info>");
        $output->writeln("");
        $output->writeln(" 📦 <info>Go to <comment>" . $packageDirectory . "</comment> and start building your package :)</info>");
        $output->writeln("");
        $output->writeln(" 🎮 <info>A playground has also been setup at <comment>" . $playgroundDirectory . ' </comment>.' . PHP_EOL . PHP_EOL . "    The playground has your new package pre-installed (symlinked from your local package folder)<info>");
        $output->writeln("    <info>Any changes made whilst developing your package will be immediately updated " . PHP_EOL . "    in the playground environment.</info>");
        $output->writeln("");
        $output->writeln("    <info>Run <comment>cd " . $playgroundDirectory . " && php artisan serve</comment> in a separate terminal window " . PHP_EOL . "    to begin the playground environment.</info>" . PHP_EOL);
    }
}
