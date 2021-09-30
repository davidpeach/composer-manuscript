<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FreshPackage extends Package
{
    public function getPath(): string
    {
        return $this->directory . '/' . $this->folderName();
    }

    public function getData(): void
    {
        $this->name = $this->determineName();
        $this->output->writeln('  <comment>' . $this->name . "</comment>\n");

        $this->description = $this->determineDescription();
        $this->output->writeln('  <comment>' . $this->description . "</comment>\n");

        $this->authorName = $this->determineAuthorName();
        $this->output->writeln('  <comment>' . $this->authorName . "</comment>\n");

        $this->authorEmail = $this->determineAuthorEmail();
        $this->output->writeln('  <comment>' . $this->authorEmail . "</comment>\n");

        $this->author = $this->authorName . ' <' . $this->authorEmail . '>';

        $this->stability = $this->determineStability();
        $this->output->writeln('  <comment>' . $this->stability . "</comment>\n");

        $this->license = $this->determineLicense();
        $this->output->writeln('  <comment>' . $this->license . "</comment>\n");
    }

    public function scaffold(): void
    {
        mkdir($this->getPath());

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
    }

    private function determineName(): string
    {
        return $this->helper->ask(
            $this->input,
            $this->output,
            new Question(' <question> Please enter the name of your package [wow/such-package] </question> : ', 'wow/such-package')
        );
    }

    private function determineDescription(): string
    {
        return $this->helper->ask(
                $this->input,
                $this->output,
                new Question(' <question> Please enter the description of your package </question> : ', '')
            ) ?? '';
    }

    private function determineAuthorName(): string
    {
        return $this->helper->ask(
            $this->input,
            $this->output,
            new Question(' <question> Please enter the author name of your package</question> : ', 'name here')
        );
    }

    private function determineAuthorEmail(): string
    {
        return $this->helper->ask(
            $this->input,
            $this->output,
            new Question(' <question> Please enter the author email of your package</question> : ', 'email@example.com')
        );
    }

    private function determineStability(): string
    {
        $question = new ChoiceQuestion(
            ' <question> Please select your minimum stability [stable] </question> : ',
            ['dev', 'alpha', 'beta', 'RC', 'stable'],
            4
        );
        $question->setErrorMessage('Minimum Stability %s is invalid.');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineLicense(): string
    {
        return $this->helper->ask(
            $this->input,
            $this->output,
            new Question(' <question> Please enter the license for your package [MIT] </question> : ', 'MIT')
        );
    }
}
