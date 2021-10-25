<?php

namespace DavidPeach\Manuscript\Playground;

use DavidPeach\Manuscript\ComposerFileManager;
use Symfony\Component\Finder\Finder;

class PlaygroundFinder
{
    const PLAYGROUND_DIRECTORY = 'playgrounds';

    public function __construct(private ComposerFileManager $composerFileManager)
    {}

    public function discover(string $root): array
    {
        $directory = $root . '/' . self::PLAYGROUND_DIRECTORY;

        $finder = new Finder;

        $finder->depth('== 0')
            ->directories()
            ->name('*')
            ->in($directory);

        $currentPlaygrounds = [];

        foreach ($finder as $file) {

            $composerData = $this->composerFileManager->read($file->getPathname());

            $playground = new Playground;
            $playground->setName($composerData['name']);
            $playground->setBaseDirectory($file->getPath());
            $playground->setPath($file->getPathname());
            $playground->setFolderName($file->getFilename());

            $currentPlaygrounds[$playground->getFolderName()] = $playground;

        }

        return $currentPlaygrounds;
    }
}
