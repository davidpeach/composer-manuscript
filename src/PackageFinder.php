<?php

namespace DavidPeach\Manuscript;

use DavidPeach\Manuscript\ComposerFileManager;
use Symfony\Component\Finder\Finder;

class PackageFinder
{
    const PACKAGE_DIRECTORY = 'packages';

    public function __construct(private ComposerFileManager $composerFileManager)
    {}

    public function discover(string $root): array
    {
        $directory = $root . '/' . self::PACKAGE_DIRECTORY;

        $finder = new Finder;

        $finder->depth('== 0')
            ->directories()
            ->name('*')
            ->in($directory);

        $currentPackages = [];

        foreach ($finder as $file) {

            $composerData = $this->composerFileManager->read($file->getPathname());

            $package = new ExistingPackage;
            $package->setName($composerData['name']);
            $package->setBaseDirectory($file->getPath());
            $package->setPath($file->getPathname());
            $package->setFolderName($file->getFilename());

            $currentPackages[$playground->getFolderName()] = $playground;

        }

        return $currentPackages;
    }
}
