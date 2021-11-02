<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Process\Process;

class GitCredentials
{
    private string $name = 'Your Name';

    private string $email = 'email@example.com';

    public function __construct()
    {
        $process = new Process(command: [
            'git',
            'config',
            '--global',
            'user.name'
        ]);

        $process->run();

        if ($process->isSuccessful()) {
            $this->setName(name: trim(string: $process->getOutput(), characters: "\n"));
        }

        $process = new Process(command: [
            'git',
            'config',
            '--global',
            'user.email'
        ]);

        $process->run();

        if ($process->isSuccessful()) {
            $this->setEmail(email: trim(string: $process->getOutput(), characters: "\n"));
        }
    }

    /**
     * @param string $name
     */
    private function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $email
     */
    private function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function guessNamespace(): string
    {
        return strtolower(
            str_replace(search: ' ', replace: '', subject: $this->name)
        );
    }
}
