<?php

namespace DavidPeach\Manuscript;

use Exception;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FreshPackage extends Package
{
    public function getPath(): string
    {
        return $this->directory . '/' . $this->determineFolderName();
    }

    public function getData(): void
    {
        $this->data['name'] = $this->determineName();
        $this->output->writeln('  <comment>' . $this->data['name'] . "</comment>\n");

        $this->data['description'] = $this->determineDescription();
        $this->output->writeln('  <comment>' . $this->data['description'] . "</comment>\n");

        $authorName = $this->determineAuthorName();
        $this->output->writeln('  <comment>' . $authorName . "</comment>\n");

        $authorEmail = $this->determineAuthorEmail();
        $this->output->writeln('  <comment>' . $authorEmail . "</comment>\n");

        $this->data['author'] = $authorName . ' <' . $authorEmail . '>';

        $this->data['stability'] = $this->determineStability();
        $this->output->writeln('  <comment>' . $this->data['stability'] . "</comment>\n");

        $this->data['license'] = $this->determineLicense();
        $this->output->writeln('  <comment>' . $this->data['license'] . "</comment>\n");
    }

    public function scaffold(): void
    {
        $fullPath = $this->getPath();

        if (file_exists($fullPath)) {
            throw new Exception($fullPath . ' already exists', 1);
        }

        mkdir($fullPath);

        $composerBuildCommand = [
            'composer init',
        ];

        foreach ($this->data as $key => $value) {
            if (!empty($this->data[$key])) {
                $composerBuildCommand[] = '--' . $key . '="' . $value . '"';
            }
        }

        $composerBuildCommand[] = '--autoload="src/"';

        $commands = [
            'cd ' . $fullPath,
            implode(' ', $composerBuildCommand),
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

    private function determineFolderName(): string
    {
        $parts = explode('/', $this->data['name']);
        return end($parts);
    }
}
