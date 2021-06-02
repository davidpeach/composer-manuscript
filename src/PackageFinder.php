<?php

namespace Davidpeach\Manuscript;

use Davidpeach\Manuscript\PackageBuilder;
use Symfony\Component\Finder\Finder;

class PackageFinder
{
    public function discover(string $directory): array
    {
        $finder = new Finder;

        $finder->depth('== 0')
            ->directories()
            ->notName('manuscript-playground-*')
            ->in($directory);

        $currentPackages = [];

        foreach ($finder as $file) {
            $package = PackageBuilder::hydrate($file);
            $currentPackages[] = $package;
        }

        return $currentPackages;
    }
}
