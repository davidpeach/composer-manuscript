<?php

namespace Davidpeach\Manuscript;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PackageInstaller
{
    public static function install($package, $playground): void
    {
        ComposerFileManager::add(
            $playground->getPath() . '/composer.json',
            ['repositories' => [
                [
                    'type' => 'path',
                    'url'  =>  realpath($package->getPath()),
                    'options' => [
                        'symlink' => true,
                    ],
                ]
            ]]
        );

        $process = Process::fromShellCommandline(
            'cd ' . $playground->getPath() . ' && composer require ' . $package->getName()
        );

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    public static function addDemoRoute($directory, $namespace): void
    {
        $routesFile = file_get_contents($directory . '/routes/web.php');

        $toAdd = "Route::get('/quote', function () {
    return " . $namespace . "Quote::random();
});";

        file_put_contents($directory . '/routes/web.php', "\n\n" . $toAdd, FILE_APPEND);
    }
}
