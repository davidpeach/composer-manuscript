<?php

namespace DavidPeach\Manuscript\Finders;

use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\Exceptions\PackageModelNotCreatedException;
use DavidPeach\Manuscript\PackageModelFactory;
use Symfony\Component\Finder\Finder;

abstract class PackageFinder
{
    /**
     * @param string $root
     * @return array
     */
    public function discover(string $root): array
    {
        $directory = $root . '/' . $this->directoryToSearch();

        $finder = new Finder;

        $finder->depth(levels: '== 0')
            ->directories()
            ->name(patterns: '*')
            ->in(dirs: $directory);

        $currentPlaygrounds = [];

        $modelFactory = new PackageModelFactory(composer: new ComposerFileManager);

        foreach ($finder as $file) {
            try {
                $playground = $modelFactory->fromPath(pathToPackage: $file->getPathname());
                $currentPlaygrounds[$playground->getFolderName()] = $playground;
            } catch (PackageModelNotCreatedException) {
                // do nothing
            }
        }

        return $currentPlaygrounds;
    }

    abstract public function directoryToSearch(): string;
}