<?php

namespace DavidPeach\Manuscript;

use DavidPeach\Manuscript\Exceptions\PackageModelNotCreatedException;
use Symfony\Component\Finder\Finder;

class Playgrounds
{
    const PLAYGROUND_DIRECTORY = 'playgrounds';

    /**
     * @param string $root
     * @return array
     */
    public function discover(string $root): array
    {
        $directory = $root . '/' . self::PLAYGROUND_DIRECTORY;

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
}