<?php

namespace Davidpeach\Manuscript;

use Illuminate\Support\Str;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Playground
{
    private $playgrounds = [
        'laravel 6.x' => '--prefer-dist laravel/laravel %s "6.*"',
        'laravel 7.x' => '--prefer-dist laravel/laravel %s "7.*"',
        'laravel 8.x' => '--prefer-dist laravel/laravel %s "8.*"',
    ];

    public function __construct($input, $output, $consoleHelper)
    {
        $this->input  = $input;
        $this->output = $output;
        $this->helper = $consoleHelper;
    }

    public function install($installDirectory)
    {
        $this->playground = $this->determinePlayground();
        $this->playgroundInstallLocation = $this->determinePlaygroundInstallLocation($installDirectory);
        $this->setupPlaygroundFolder();
    }

    public function getDirectory()
    {
        return $this->playgroundInstallLocation;
    }

    private function determinePlayground()
    {
        $question = new ChoiceQuestion(
            'Please select your playground',
            array_keys($this->playgrounds),
            0
        );
        $question->setErrorMessage('Playground %s is invalid.');

        $this->chosenPlayground = $this->helper->ask($this->input, $this->output, $question);
        $this->output->writeln('<comment>' . $this->chosenPlayground . '</comment>');

        return $this->playgrounds[$this->chosenPlayground];
    }

    private function determinePlaygroundInstallLocation($installDirectory)
    {
        $folder = Str::slug($this->chosenPlayground) . '-workspace-' . time();

        return $installDirectory . $folder;
    }

    private function setupPlaygroundFolder()
    {
        $installPlaygroundCmd = sprintf(
            $this->playground,
            $this->playgroundInstallLocation
        );
        $process = Process::fromShellCommandline('composer create-project ' . $installPlaygroundCmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    public function getPlaygroundInstallLocation()
    {
        return $this->playgroundInstallLocation;
    }
}
