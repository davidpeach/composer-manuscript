<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use Carbon\Carbon;
use DavidPeach\Manuscript\Frameworks\Framework;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PlaygroundPackageBuilder implements PackageBuilderContract
{
    public function __construct(
        private string    $root,
        private Framework $framework,
    )
    {
    }

    /**
     * @return string
     */
    public function build(): string
    {
        $folder = vsprintf(
            format: '%s-%s',
            values: [
                $this->framework->folderFormat(),
                Carbon::now()->timestamp,
            ]
        );

        $installCommand = vsprintf(
            format: $this->framework->getInstallCommandSegment(),
            values: [$this->root . '/' . $folder]
        );

        $process = Process::fromShellCommandline(command: 'composer create-project ' . $installCommand);
        $process->setTimeout(timeout: 3600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException(process: $process);
        }

        return $this->root . '/' . $folder;
    }
}
