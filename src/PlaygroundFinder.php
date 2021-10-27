<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Finder\Finder;

class PlaygroundFinder
{
    const PLAYGROUND_DIRECTORY = 'playgrounds';

    public function discover(string $root): array
    {
        $directory = $root . '/' . self::PLAYGROUND_DIRECTORY;

        $finder = new Finder;

        $finder->depth('== 0')
            ->directories()
            ->name('*')
            ->in($directory);

        $currentPlaygrounds = [];

        $modelFactory = new PackageModelFactory(new ComposerFileManager);

        foreach ($finder as $file) {
            $playground = $modelFactory->fromPath($file->getPathname());
            $currentPlaygrounds[$playground->getFolderName()] = $playground;
        }

        return $currentPlaygrounds;
    }
}
