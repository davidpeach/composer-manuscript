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

    public static function addDemoRoute($directory, $namespace)
    {
        $routesFile = file_get_contents($directory . '/routes/web.php');

        $toAdd = "Route::get('/quote', function () {
                return " . $namespace . "Quote::random();
            });";

        file_put_contents($directory . '/routes/web.php', "\n\n" . $toAdd, FILE_APPEND);
    }
}
