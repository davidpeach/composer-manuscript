<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Helpers
{
    public static function determineHomeDirectory(): string
    {
        $process = Process::fromShellCommandline('echo $( getent passwd "$USER" | cut -d: -f6 )');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim($process->getOutput());
    }
}
