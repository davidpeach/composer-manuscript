<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use DavidPeach\Manuscript\GitCredentials;
use DavidPeach\Manuscript\Feedback;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;
use Exception;

class BasicPackageBuilder implements PackageBuilderContract
{

    public function __construct(
        private string         $root,
        private Feedback       $feedback,
        private GitCredentials $gitCredentials,
    )
    {
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function build(): string
    {
        $name = $this->determineName();
        $description = $this->determineDescription();
        $authorName = $this->determineAuthorName();
        $authorEmail = $this->determineAuthorEmail();
        $author = $authorName . ' <' . $authorEmail . '>';
        $stability = $this->determineStability();
        $license = $this->determineLicense();

        $parts = explode('/', $name);
        $path = $this->root . '/' . end($parts);

        try {
            $fs = new Filesystem;

            if ($fs->exists(files: $path)) {
                throw new Exception(message: "Package folder name already exists");
            }

            $fs->mkdir(dirs: $path);
        } catch (Throwable $e) {
            throw $e;
        }

        $composerBuildCommand = implode(
            separator: ' ',
            array: [
                'composer init',
                sprintf('--name="%s"', $name),
                sprintf('--description="%s"', $description),
                sprintf('--author="%s"', $author),
                sprintf('--stability="%s"', $stability),
                sprintf('--license="%s"', $license),
                '--autoload="src/"',
            ]);

        $commands = [
            'cd ' . $path,
            $composerBuildCommand,
            'cd ../',
        ];

        $process = Process::fromShellCommandline(implode(separator: ' && ', array: $commands));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException(process: $process);
        }

        return $path;
    }

    /**
     * @return string
     */
    private function determineName(): string
    {
        $question = vsprintf(
            format: 'Please enter the full name of your package with namespace [%s/package-name]',
            values: [
                $this->gitCredentials->guessNamespace()
            ]
        );

        $answer = vsprintf(
            format: '%s/package-name',
            values: [
                $this->gitCredentials->guessNamespace()
            ]
        );

        return $this->feedback->ask(question: $question, defaultAnswer: $answer);
    }

    /**
     * @return string
     */
    private function determineDescription(): string
    {
        $question = 'Please enter the description of your package : ';

        return $this->feedback->ask(question: $question, defaultAnswer: 'Default description set by Manuscript');
    }

    /**
     * @return string
     */
    private function determineAuthorName(): string
    {
        $question = vsprintf(
            format: 'Please enter the author name of your package [%s]',
            values: [
                $this->gitCredentials->getName()
            ]
        );

        return $this->feedback->ask(question: $question, defaultAnswer: $this->gitCredentials->getName());
    }

    /**
     * @return string
     */
    private function determineAuthorEmail(): string
    {
        $question = vsprintf(
            format: 'Please enter the author email of your package [%s]',
            values: [
                $this->gitCredentials->getEmail()
            ]
        );

        return $this->feedback->ask(question: $question, defaultAnswer: $this->gitCredentials->getEmail());
    }

    /**
     * @return string
     */
    private function determineStability(): string
    {
        $question = 'Please select your minimum stability [stable]';

        return $this->feedback->ask(question: $question, defaultAnswer: 'stable');
    }

    /**
     * @return string
     */
    private function determineLicense(): string
    {
        $question = 'Please enter the license for your package [MIT]';

        return $this->feedback->ask(question: $question, defaultAnswer: 'MIT');
    }
}
