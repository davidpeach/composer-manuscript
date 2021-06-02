<?php

namespace Davidpeach\Manuscript;

use Symfony\Component\Finder\SplFileInfo;

class PackageBuilder
{
    public static function hydrate(SplFileInfo $file)
    {
        $package = new ExistingPackage;
        $package->setPath($file->getRealpath());

        $package->determineName();
        $package->determineNamespace();
        $package->determineDependancies();

        return $package;
    }
}
