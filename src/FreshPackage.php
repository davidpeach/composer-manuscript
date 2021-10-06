<?php

namespace DavidPeach\Manuscript;

use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;

class FreshPackage extends Package
{
    private GitCredentials $gitCredentials;

    public function getData(): self
    {
        $this->gitCredentials = new GitCredentials;

        $this->name = $this->determineName();
        $this->description = $this->determineDescription();
        $this->authorName = $this->determineAuthorName();
        $this->authorEmail = $this->determineAuthorEmail();
        $this->author = $this->authorName . ' <' . $this->authorEmail . '>';
        $this->stability = $this->determineStability();
        $this->license = $this->determineLicense();

        $this->setPath($this->directory . $this->folderName());

        return $this;
    }

    public function scaffold(): self
    {
        try {
            $fs = new Filesystem;

            if ($fs->exists($this->getPath())) {
                throw new Exception("Package folder name already exists");
            }

            $fs->mkdir($this->getPath());
        } catch (Throwable $e) {
            throw $e;
        }


        $composerBuildCommand = implode(' ', [
            'composer init',
            sprintf('--name="%s"', $this->name),
            sprintf('--description="%s"', $this->description),
            sprintf('--author="%s"', $this->author),
            sprintf('--stability="%s"', $this->stability),
            sprintf('--license="%s"', $this->license),
            '--autoload="src/"',
        ]);

        $commands = [
            'cd ' . $this->getPath(),
            $composerBuildCommand,
            'cd ../',
        ];

        $process = Process::fromShellCommandline(implode(' && ', $commands));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this;
    }

    private function determineName(): string
    {
        $question = sprintf(
            '<question>Please enter the full name of your package with namespace [%s/package-name] </question> : ',
            $this->gitCredentials->guessNamespace('your-namespace'),
        );

        $answer = sprintf(
            '%s/package-name',
            $this->gitCredentials->guessNamespace('your-namespace'),
        );

        return $this->questions->question($question)->defaultAnswer($answer)->ask();
    }

    private function determineDescription(): string
    {
        $question = '<question> Please enter the description of your package </question> : ';

        $answer = 'Default description set by Manuscript';

        return $this->questions->question($question)->defaultAnswer($answer)->ask();
    }

    private function determineAuthorName(): string
    {
        $question = sprintf(
            ' <question> Please enter the author name of your package [%s]</question> : ',
            $this->gitCredentials->getName('Your Name')
        );

        $answer = $this->gitCredentials->getName('Your Name');

        return $this->questions->question($question)->defaultAnswer($answer)->ask();
    }

    private function determineAuthorEmail(): string
    {
        $question = sprintf(
            ' <question> Please enter the author email of your package [%s]</question> : ',
            $this->gitCredentials->getEmail('email@example.com')
        );

        $answer = $this->gitCredentials->getEmail('email@example.com');

        return $this->questions->question($question)->defaultAnswer($answer)->ask();
    }

    private function determineStability(): string
    {
        $question = ' <question> Please select your minimum stability [stable] </question> : ';

        $answer = 'stable';

        return $this->questions->question($question)->defaultAnswer($answer)->ask();
    }

    private function determineLicense(): string
    {
        $question = '<question> Please enter the license for your package [MIT] </question>';

        $answer = 'MIT';

        return $this->questions->question($question)->defaultAnswer($answer)->ask();
    }
}
