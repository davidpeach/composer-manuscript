<?php

namespace DavidPeach\Manuscript\Finders;

use DavidPeach\Manuscript\Exceptions\PackageModelNotCreatedException;
use DavidPeach\Manuscript\ModelFactory;
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

        $modelFactory = $this->getModelFactory();

        foreach ($finder as $file) {
            try {
                $package = $modelFactory->fromPath(pathToPackage: $file->getPathname());
                $currentPlaygrounds[$package->getFolderName()] = $package;
            } catch (PackageModelNotCreatedException) {
                // do nothing
            }
        }

        return $currentPlaygrounds;
    }

    abstract public function directoryToSearch(): string;

    abstract protected function getModelFactory(): ModelFactory;
}