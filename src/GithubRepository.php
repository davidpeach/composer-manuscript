<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Process\Process;

class GithubRepository
{
    private string $localDirectory;

    private string $remoteUrl;

    private bool $clonedSuccessfully;

    public function setLocalDirectory(string $localDirectory): self
    {
        $this->localDirectory = $localDirectory;

        return $this;
    }

    public function getLocalDirectory(): string
    {
        return $this->localDirectory;
    }

    public function setRemoteUrl(string $remoteUrl): self
    {
        $this->remoteUrl = $remoteUrl;

        return $this;
    }

    public function clone(): self
    {
        $process = new Process(command: [
            'git',
            'clone',
            $this->remoteUrl,
            $this->localDirectory,
        ]);

        $process->run();

        if ($process->isSuccessful()) {
            $this->clonedSuccessfully = true;
            return $this;
        }

        $this->clonedSuccessfully = false;

        return $this;
    }

    public function clonedSuccessfully(): bool
    {
        return $this->clonedSuccessfully;
    }
}
