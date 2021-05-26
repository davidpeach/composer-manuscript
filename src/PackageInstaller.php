<?php

namespace Davidpeach\Manuscript;

use Davidpeach\Manuscript\AddsToJsonFile;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PackageInstaller
{
    public static function install(
        string $playgroundDirectory,
        string $packageDirectory,
        string $packageName
    )
    {
        AddsToJsonFile::add(
            $playgroundDirectory . '/composer.json',
            ['repositories' => [
                [
                    'type' => 'path',
                    'url'  =>  realpath($packageDirectory),
                    'options' => [
                        'symlink' => true,
                    ],
                ]
            ]]
        );

        $process = Process::fromShellCommandline(
            'cd ' . $playgroundDirectory . ' && composer require ' . $packageName
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
