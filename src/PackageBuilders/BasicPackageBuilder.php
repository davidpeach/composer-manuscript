<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use DavidPeach\Manuscript\GitCredentials;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;
use Exception;

class BasicPackageBuilder implements PackageBuilderContract
{
    private ?string $root = null;

    private ?SymfonyStyle $io = null;

    public function __construct(
        private GitCredentials $gitCredentials,
    )
    {
    }

    public function setRoot(string $root): self
    {
        $this->root = $root;

        return $this;
    }

    public function setIO(StyleInterface $io): self
    {
        $this->io = $io;

        return $this;
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function build(): string
    {
        $authorName = $this->determineAuthorName();
        $authorEmail = $this->determineAuthorEmail();
        $namespace = $this->determineNamespace();
        $packageName = $this->determinePackageName();

        $description = $this->determineDescription();
        $author = $authorName . ' <' . $authorEmail . '>';
        $stability = $this->determineStability();
        $license = $this->determineLicense();

        $fullPackageName = implode(separator: '/', array: [$namespace, $packageName]);

        $packagePath = $this->root . '/' . $packageName;

        $fs = new Filesystem;

        if ($fs->exists(files: $packagePath)) {
            throw new Exception(message: "Package folder name already exists");
        }

        $fs->mkdir(dirs: $packagePath);

        $composerBuildCommand = implode(
            separator: ' ',
            array: [
                'composer init',
                vsprintf(format: '--name="%s"', values: [$fullPackageName]),
                vsprintf(format: '--description="%s"', values: [$description]),
                vsprintf(format: '--author="%s"', values: [$author]),
                vsprintf(format: '--stability="%s"', values: [$stability]),
                vsprintf(format: '--license="%s"', values: [$license]),
                '--autoload="src/"',
            ]);

        $commands = [
            'cd ' . $packagePath,
            $composerBuildCommand,
            'cd ../',
        ];

        $process = Process::fromShellCommandline(implode(separator: ' && ', array: $commands));
        $process->setTimeout(timeout: 3600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException(process: $process);
        }

        return $packagePath;
    }

    /**
     * @return string
     */
    private function determineNamespace(): string
    {
        // validation needed
        return $this->io->ask(
            question: 'Please enter your package namespace.',
            default: $this->gitCredentials->guessNamespace(),
        );
    }

    private function determinePackageName(): string
    {
        // validation needed
        return $this->io->ask(
            question: 'Please enter your package name.',
            default: 'my-new-package',
        );
    }

    /**
     * @return string
     */
    private function determineDescription(): string
    {
        // validation needed
        return $this->io->ask(
            question: 'Please enter the description of your package.',
            default: 'Default description set by Manuscript'
        );
    }

    /**
     * @return string
     */
    private function determineAuthorName(): string
    {
        // validation needed
        return $this->io->ask(
            question: 'Please enter the author name of your package.',
            default: $this->gitCredentials->getName()
        );
    }

    /**
     * @return string
     */
    private function determineAuthorEmail(): string
    {
        // validation needed
        return $this->io->ask(
            question: 'Please enter the author email of your package.',
            default: $this->gitCredentials->getEmail()
        );
    }

    /**
     * @return string
     */
    private function determineStability(): string
    {
        // validation needed, or choice question
        return $this->io->ask(
            question: 'Please select your minimum stability.',
            default: 'stable'
        );
    }

    /**
     * @return string
     */
    private function determineLicense(): string
    {
        return $this->io->ask(
            question: 'Please enter the license for your package.',
            default: 'MIT'
        );
    }
}
