<?php

namespace DavidPeach\Manuscript\Utilities;

use DavidPeach\Manuscript\Exceptions\ComposerFileNotFoundException;
use DavidPeach\Manuscript\Exceptions\PackageInstallFailedException;
use DavidPeach\Manuscript\Models\PackageModel;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PackageInstaller
{
    public function __construct(
        private ComposerFileManager $composer
    ) {
    }

    /**
     * @param PackageModel $package
     * @param PackageModel $playground
     * @throws PackageInstallFailedException
     */
    public function install(PackageModel $package, PackageModel $playground): void
    {
        try {
            $this->composer->add(
                pathToFile: $playground->getPath() . '/composer.json',
                toAdd: [
                    'minimum-stability' => 'dev',
                    'repositories' => [
                        [
                            'type' => 'path',
                            'url'  =>  realpath($package->getPath()),
                            'options' => [
                                'symlink' => true,
                            ],
                        ]
                    ]
                ]
            );

            $process = Process::fromShellCommandline(
                command: 'cd ' . $playground->getPath() . ' && composer require ' . $package->getName()
            );

            $process->setTimeout(timeout: 3600);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException(process: $process);
            }
        } catch (ComposerFileNotFoundException $e) {
            throw new PackageInstallFailedException(
                message: 'Failed to install package.',
                code: $e->getCode(),
                previous: $e
            );
        }
    }
}
