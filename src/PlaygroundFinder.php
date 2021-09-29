<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Finder\Finder;

class PlaygroundFinder
{
    public function discover(string $directory): array
    {
        $finder = new Finder;

        $finder->depth('== 0')
            ->directories()
            ->name('manuscript-playground-*')
            ->in($directory);

        $currentPlaygrounds = [];

        foreach ($finder as $file) {
            $playground = PlaygroundBuilder::hydrate($file);
            $currentPlaygrounds[$playground->getPath()] = $playground;
        }

        return $currentPlaygrounds;
    }
}
