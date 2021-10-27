<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use DavidPeach\Manuscript\GitCredentials;
use DavidPeach\Manuscript\QuestionAsker;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;
use Exception;

class BasicPackageBuilder implements PackageBuilderContract
{

    public function __construct(
        private string $root,
        private QuestionAsker $questions,
        private GitCredentials $gitCredentials,
    ){}

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

            if ($fs->exists($path)) {
                throw new Exception("Package folder name already exists");
            }

            $fs->mkdir($path);
        } catch (Throwable $e) {
            throw $e;
        }

        $composerBuildCommand = implode(' ', [
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

        $process = Process::fromShellCommandline(implode(' && ', $commands));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $path;
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
