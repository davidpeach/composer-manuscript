<?php

namespace DavidPeach\Manuscript\Github;

use Symfony\Component\Process\Process;

class GithubRepository
{
    private string $localDirectory;

    private string $remoteUrl;

    private bool $clonedSuccessfully = false;

    private string $workingDirectory;

    public function setWorkingDirectory(string $workingDirectory): self
    {
        $this->workingDirectory = $workingDirectory;

        return $this;
    }

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
        $this->clonedSuccessfully = false;

        $process = new Process(command: [
            'git',
            'clone',
            $this->remoteUrl,
            $this->localDirectory,
        ]);

        $process->setWorkingDirectory($this->workingDirectory);
        $process->setTimeout(timeout: 3600);
        $process->run();

        if ($process->isSuccessful()) {
            $this->clonedSuccessfully = true;
            return $this;
        }

        $this->clonedSuccessfully = false;

        sleep(3);

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
