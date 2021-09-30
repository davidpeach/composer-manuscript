<?php

namespace DavidPeach\Manuscript\Playground;

use Symfony\Component\Finder\Finder;

class PlaygroundFinder
{
    public function discover(string $directory): array
    {
        $finder = new Finder;

        $finder->depth('== 0')
            ->directories()
            ->name('*')
            ->in($directory);

        $currentPlaygrounds = [];

        foreach ($finder as $file) {
            $playground = PlaygroundBuilder::hydrate($file);
            $currentPlaygrounds[$playground->getFolderName()] = $playground;
        }

        return $currentPlaygrounds;
    }
}
