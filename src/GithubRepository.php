<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Process\Process;

class GithubRepository
{
    private string $localDirectory;

    private string $remoteUrl;

    private bool $clonedSuccessfully;

    /**
     * @param string $localDirectory
     * @return $this
     */
    public function setLocalDirectory(string $localDirectory): self
    {
        $this->localDirectory = $localDirectory;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocalDirectory(): string
    {
        return $this->localDirectory;
    }

    /**
     * @param string $remoteUrl
     * @return $this
     */
    public function setRemoteUrl(string $remoteUrl): self
    {
        $this->remoteUrl = $remoteUrl;

        return $this;
    }

    /**
     * @return $this
     */
    public function clone(): self
    {
        $process = new Process(command: [
            'git',
            'clone',
            $this->remoteUrl,
            $this->localDirectory,
        ]);

        $process->setTimeout(timeout: 3600);
        $process->run();

        if ($process->isSuccessful()) {
            $this->clonedSuccessfully = true;
            return $this;
        }

        $this->clonedSuccessfully = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function clonedSuccessfully(): bool
    {
        return $this->clonedSuccessfully;
    }
}
