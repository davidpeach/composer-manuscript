<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Process\Process;

class GitCredentials
{
    private string $name = '';

    private string $email = '';

    public function __construct()
    {
        $process = new Process([
            'git',
            'config',
            '--global',
            'user.name'
        ]);

        $process->run();

        if ($process->isSuccessful()) {
            $this->setName(trim($process->getOutput(), "\n"));
        }


        $process = new Process([
            'git',
            'config',
            '--global',
            'user.email'
        ]);

        $process->run();

        if ($process->isSuccessful()) {
            $this->setEmail(trim($process->getOutput(), "\n"));
        }
    }

    private function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(string $fallback): string
    {
        if (empty($this->name)) {
            return $fallback;
        }

        return $this->name;
    }

    private function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(string $fallback): string
    {
        if (empty($this->email)) {
            return $fallback;
        }

        return $this->email;
    }

    public function guessNamespace(string $fallback): string
    {
        $possibleNamespace = strtolower(
            str_replace(' ', '', $this->name)
        );

        if (! empty($possibleNamespace)){
            return $possibleNamespace;
        }

        return $fallback;
    }
}
