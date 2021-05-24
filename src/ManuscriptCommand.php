<?php

namespace Davidpeach\Manuscript;

use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ManuscriptCommand extends Command
{
    protected static $defaultName = 'setup';

    private $helper;

    private $cwd;

    private $installDirectory;

    private $packageName;

    private $packageDescription;

    private $packageAuthor;

    private $packageMinimumStability;

    private $packageLicense;

    private $packageDirectory;

    private $packageNameSpace;

    private $packageFramework;

    private $packageFrameworkInstallLocation;

    private $frameworks = [
        'laravel 6.x' => '--prefer-dist laravel/laravel %s "6.*"',
        'laravel 7.x' => '--prefer-dist laravel/laravel %s "7.*"',
        'laravel 8.x' => '--prefer-dist laravel/laravel %s "8.*"',
    ];

    private $chosenFramework;

    protected function configure(): void
    {
        $this
            ->addOption('install-dir', null, InputOption::VALUE_OPTIONAL, 'The directory to setup the environment in.')
            ->setHelp('This command allows you to create a composer package...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->helper = $this->getHelper('question');

        $this->cwd = getcwd();

        $this->installDirectory = $this->determineInstallDirectory($input, $output);

        // composer json values
        $this->packageName = $this->determinePackageName($input, $output);
        $output->writeln('<comment>' . $this->packageName . "</comment>\n");

        $this->packageDescription = $this->determinePackageDescription($input, $output);
        $output->writeln('<comment>' . $this->packageDescription . "</comment>\n");

        $this->packageAuthor = $this->determinePackageAuthor($input, $output);
        $output->writeln('<comment>' . $this->packageAuthor . "</comment>\n");

        $this->packageMinimumStability = $this->determinePackageMinimumStability($input, $output);
        $output->writeln('<comment>' . $this->packageMinimumStability . "</comment>\n");

        $this->packageLicense = $this->determinePackageLicense($input, $output);
        $output->writeln('<comment>' . $this->packageLicense . "</comment>\n");

        $this->packageDirectory = $this->determinePackageDirectory($input, $output);
        $this->packageNameSpace = $this->determinePackageNameSpace();

        $this->packageFramework = $this->determinePackageFramework($input, $output);
        $this->packageFrameworkInstallLocation = $this->determinePackageFrameworkInstallLocation($input, $output);

        $this->createEmptyPackageFolder();
        $this->setupPackageFrameworkFolder();


        $this->writeSummary($output);

        return Command::SUCCESS;
    }

    private function determineInstallDirectory($input, $output)
    {
        $installDirectory = $input->getOption('install-dir');

        if (! $installDirectory) {
            return $this->cwd . '/';
        }

        if (! file_exists($this->cwd . '/' . $input->getOption('install-dir'))) {
            return $this->cwd . '/';
        }

        return $this->cwd . '/' . $input->getOption('install-dir') . '/';
    }

    private function determinePackageName($input, $output): string
    {
        $question = new Question('<question>Please enter the name of your package [wow/such-package]</question> : ', 'wow/such-package');

        return $this->helper->ask($input, $output, $question);
    }

    private function determinePackageDescription($input, $output): string
    {
        $question = new Question('<question>Please enter the description of your package</question> : ', '');

        return $this->helper->ask($input, $output, $question);
    }

    private function determinePackageAuthor($input, $output): string
    {
        $name  = '';
        $email = '';

        $process = new Process([
            'git',
            'config',
            '--global',
            'user.name'
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $name = trim($process->getOutput(), "\n");

        $process = new Process([
            'git',
            'config',
            '--global',
            'user.email'
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $email = trim($process->getOutput(), "\n");

        $determinedAuthorDetails = sprintf('%s <%s>', $name, $email);

        $question = new Question('<question>Please confirm the package author details [' . $determinedAuthorDetails . ']</question> : ', $determinedAuthorDetails);

        return $this->helper->ask($input, $output, $question);
    }

    private function determinePackageMinimumStability($input, $output): string
    {
        //dev, alpha, beta, RC, and stable.
        $question = new ChoiceQuestion(
            '<question>Please select your minimum stability [stable]</question> : ',
            ['dev', 'alpha', 'beta', 'RC', 'stable'],
            4
        );
        $question->setErrorMessage('Minimum Stability %s is invalid.');

        return $this->helper->ask($input, $output, $question);
    }

    private function determinePackageLicense($input, $output): string
    {
        $question = new Question('<question>Please enter the license for your package [MIT]</question> : ', 'MIT');

        return $this->helper->ask($input, $output, $question);
    }

    private function determinePackageDirectory($input, $output): string
    {
        $packageFolderName = str_replace('/', '-', $this->packageName);
        return $this->installDirectory . $packageFolderName;
    }

    private function determinePackageFramework($input, $output)
    {
        $question = new ChoiceQuestion(
            'Please select your framework',
            array_keys($this->frameworks),
            0
        );
        $question->setErrorMessage('Framework %s is invalid.');

        $this->chosenFramework = $this->helper->ask($input, $output, $question);
        $output->writeln('<comment>' . $this->chosenFramework . '</comment>');

        return $this->frameworks[$this->chosenFramework];
    }

    private function determinePackageFrameworkInstallLocation($input, $output)
    {
        $folder = Str::slug($this->chosenFramework) . '-workspace-' . time();

        return $this->installDirectory . $folder;
    }

    private function determinePackageNameSpace()
    {
        $parts = explode('/', $this->packageName);
        $firstPart = Str::studly($parts[0]);
        $secondPart = Str::studly($parts[1]);

        return implode('\\', [$firstPart, $secondPart]) . '\\';
    }

    private function createEmptyPackageFolder()
    {
        if (file_exists($this->packageDirectory)) {
            throw new \Exception($this->packageDirectory . ' already exists', 1);
        }

        mkdir($this->packageDirectory);

        $composerBuildCommand = [
            'composer init',
        ];

        if (! empty($this->packageName)) {
            $composerBuildCommand[] = '--name="' . $this->packageName . '"';
        }

        if (! empty($this->packageDescription)) {
            $composerBuildCommand[] = '--description="' . $this->packageDescription . '"';
        }

        if (! empty($this->packageAuthor)) {
            $composerBuildCommand[] = '--author="' . $this->packageAuthor . '"';
        }

        if (! empty($this->packageMinimumStability)) {
            $composerBuildCommand[] = '--stability="' . $this->packageMinimumStability . '"';
        }

        if (! empty($this->packageLicense)) {
            $composerBuildCommand[] = '--license="' . $this->packageLicense . '"';
        }

        $commands = [
            'cd ' . $this->packageDirectory,
            implode(' ', $composerBuildCommand),
            'cd ' . $this->cwd,
        ];

        $process = Process::fromShellCommandline(implode(' && ', $commands));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        mkdir($this->packageDirectory . '/src');

        $composerFile = file_get_contents($this->packageDirectory . '/composer.json');

        $composerArray = json_decode($composerFile, true);
        $composerArray['autoload'] = [];
        $composerArray['autoload']['psr-4'] = [
            $this->packageNameSpace => 'src/',
        ];

        $updatedComposerJson = json_encode($composerArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        file_put_contents($this->packageDirectory . '/composer.json', $updatedComposerJson);
    }

    private function setupPackageFrameworkFolder()
    {
        $installFrameworkCmd = sprintf(
            $this->packageFramework,
            $this->packageFrameworkInstallLocation
        );
        $process = Process::fromShellCommandline('composer create-project ' . $installFrameworkCmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $composerFileForFramework = file_get_contents($this->packageFrameworkInstallLocation . '/composer.json');

        $composerArray = json_decode($composerFileForFramework, true);
        $composerArray['repositories'] = [];
        $composerArray['repositories'][] = [
            'type' => 'path',
            'url'  => $this->packageDirectory,
            'options' => [
                'symlink' => true,
            ],
        ];

        $updatedComposerJson = json_encode($composerArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
        file_put_contents($this->packageFrameworkInstallLocation . '/composer.json', $updatedComposerJson);

        $process = Process::fromShellCommandline('cd ' . $this->packageFrameworkInstallLocation . ' && composer require ' . $this->packageName);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    private function writeSummary($output)
    {
        $output->writeln("\n<info>Setup complete!</info>");
        $output->writeln("<info>Thank You for using Manuscript.</info>");
        $output->writeln("");
        $output->writeln("<info>Go to <comment>" . realpath($this->packageDirectory) . "</comment> " . PHP_EOL . "and start building your package :)</info>");
        $output->writeln("");
        $output->writeln("<info>A site has also been setup at " . PHP_EOL . "<comment>" . realpath($this->packageFrameworkInstallLocation) . ' </comment>' . PHP_EOL . "with your new package pre-installed (symlinked from your local package folder)<info>");
        $output->writeln("<info>Any changes made whilst developing your package will be immediately updated " . PHP_EOL . "in the laravel test environment.</info>");
        $output->writeln("");
        $output->writeln("<info>Run <comment>cd " . realpath($this->packageFrameworkInstallLocation) . " && php artisan serve</comment> " . PHP_EOL . "in a separate terminal window to begin the laravel test environment.</info>");
    }
}