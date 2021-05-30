<?php

namespace Davidpeach\Manuscript;

use Davidpeach\Manuscript\Frameworks\Framework;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PlaygroundBuilder
{
    public static function build(Framework $framework, string $directory): Playground
    {
        $playground = new Playground;
        $playground->setBaseDirectory($directory);
        $playground->setFramework($framework);
        $playground->determinePath();

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

    public static function hydrate(SplFileInfo $file)
    {
        $path = $file->getPathname();

        $playground = new Playground;
        $playground->setBaseDirectory($file->getPath());
        // $playground->setFramework(); // determine from folder name
        $playground->setPath($file->getPathname());
        return $playground;
    }
}