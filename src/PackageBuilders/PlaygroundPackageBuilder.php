<?php

namespace DavidPeach\Manuscript\PackageBuilders;

use Carbon\Carbon;
use DavidPeach\Manuscript\Frameworks\Framework;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PlaygroundPackageBuilder implements PackageBuilderContract
{
    public function __construct(
        private string $root,
        private Framework $framework,
    ){}

    public function build(): string
    {
        $folder = vsprintf('%s-%s', [
            $this->framework->folderFormat(),
            Carbon::now()->timestamp,
        ]);

        $installCommand = sprintf(
            $this->framework->getInstallCommandSegment(),
            $this->root . '/' . $folder
        );

        $process = Process::fromShellCommandline('composer create-project ' . $installCommand);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this->root . '/' . $folder;
    }
}
