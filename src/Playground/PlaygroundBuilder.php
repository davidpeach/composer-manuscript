<?php

namespace DavidPeach\Manuscript\Playground;

use DavidPeach\Manuscript\Frameworks\Framework;
use DavidPeach\Manuscript\Package;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PlaygroundBuilder
{
    private Package $package;

    public function build(Framework $framework, string $directory): Playground
    {
        $playground = new Playground;
        $playground->setBaseDirectory($directory);
        $playground->setFramework($framework);
        $playground->setPackage($this->package);

        $folder = vsprintf($playground->getFolderFormat(), [
            $framework->folderFormat(),
            $this->package->folderName(),
        ]);

        $playground->setPath($directory . $folder);

        $installCommand = sprintf(
            $framework->getInstallCommmandSegment(),
            $playground->getPath()
        );

        $process = Process::fromShellCommandline('composer create-project ' . $installCommand);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $playground;
    }

    public static function hydrate(SplFileInfo $file): Playground
    {
        $playground = new Playground;
        $playground->setBaseDirectory($file->getPath());
        $playground->setPath($file->getPathname());
        return $playground;
    }

    public function forPackage(Package $package): self
    {
        $this->package = $package;

        return $this;
    }
}
