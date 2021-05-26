<?php

namespace Davidpeach\Manuscript;

use Davidpeach\Manuscript\AddsToJsonFile;
use Illuminate\Support\Str;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FreshPackage extends Package
{
    public function getDirectory()
    {
        return $this->installDirectory . '/' . $this->folderName;
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getData()
    {
        $this->data['name'] = $this->determineName();
        $this->output->writeln('<comment>' . $this->data['name'] . "</comment>\n");

        $this->data['description'] = $this->determineDescription();
        $this->output->writeln('<comment>' . $this->data['description'] . "</comment>\n");

        $this->data['author'] = $this->determineAuthor();
        $this->output->writeln('<comment>' . $this->data['author'] . "</comment>\n");

        $this->data['stability'] = $this->determineStability();
        $this->output->writeln('<comment>' . $this->data['stability'] . "</comment>\n");

        $this->data['license'] = $this->determineLicense();
        $this->output->writeln('<comment>' . $this->data['license'] . "</comment>\n");

        $this->namespace = $this->determineNameSpace();
        $this->folderName = $this->determineFolderName();
    }

    public function scaffold()
    {
        $fullPath = $this->getDirectory();

        if (file_exists($fullPath)) {
            throw new \Exception($fullPath . ' already exists', 1);
        }

        mkdir($fullPath);

        $composerBuildCommand = [
            'composer init',
        ];

        foreach ($this->data as $key => $value) {
            if (! empty($this->data[$key])) {
                $composerBuildCommand[] = '--' . $key . '="' . $value . '"';
            }
        }

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

        mkdir($fullPath . '/src');

        AddsToJsonFile::add(
            $fullPath . '/composer.json',
            ['autoload' => [
                'psr-4' => [
                    $this->namespace => 'src/',
                ],
            ]]
        );
    }

    private function determineName(): string
    {
        $question = new Question('<question>Please enter the name of your package [wow/such-package]</question> : ', 'wow/such-package');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineDescription(): string
    {
        $question = new Question('<question>Please enter the description of your package</question> : ', '');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineAuthor(): string
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

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineStability(): string
    {
        $question = new ChoiceQuestion(
            '<question>Please select your minimum stability [stable]</question> : ',
            ['dev', 'alpha', 'beta', 'RC', 'stable'],
            4
        );
        $question->setErrorMessage('Minimum Stability %s is invalid.');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineLicense(): string
    {
        $question = new Question('<question>Please enter the license for your package [MIT]</question> : ', 'MIT');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    private function determineFolderName(): string
    {
        return str_replace('/', '-', $this->data['name']);
    }

    private function determineNameSpace()
    {
        $parts = explode('/', $this->data['name']);
        $firstPart = Str::studly($parts[0]);
        $secondPart = Str::studly($parts[1]);

        return implode('\\', [$firstPart, $secondPart]) . '\\';
    }
}
