<?php

namespace Davidpeach\Manuscript;

class PackageDependancyFixer
{
    /**
     * If any of the packages depend on another local package, 
     * we need to change the dependancy version constraint
     * to "*" to allow installing alongside others.
     * @return [type] [description]
     */
    public function adjust()
    {
        foreach ($existingPackages as $packageToInstall) {
            foreach ($otherPackages as $packageToCheck) {
                // if packageToInstall has a dependancy of p
                // .ackageToCheck
                // create a temp file with correct version of that package
                // change version to "*"
            }
        }
    }

    /**
     * Revert the package versions back to their correct
     * original versions for use after composer has
     * installed all local development packages.
     * @return [type] [description]
     */
    public function revert()
    {
        foreach ($existingPackages as $package) {
            // if package has a .manuscript file
            // go through original depts and revert the versions in composer json file.
        }
    }
}
