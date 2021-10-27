<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PackageInstaller
{
    public function __construct(
        private ComposerFileManager $composerFileManager
    ){}

    public function install(PackageModel $package, PackageModel $playground): void
    {
        $this->composerFileManager->add(
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
}
