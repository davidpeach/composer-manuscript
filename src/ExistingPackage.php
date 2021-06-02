<?php

namespace Davidpeach\Manuscript;

class ExistingPackage extends Package
{
    public function getPath(): string
    {
        return getcwd();
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    public function getNameSpace(): string
    {
        return $this->namespace;
    }

    public function getData(): void
    {
        $composerArray = json_decode(file_get_contents($this->getPath() . '/composer.json'), true);
        $this->data['name'] = $composerArray['name'];

        // PackageFinder class to get all other packages.
            // foreach package, see if it has any of the other packages as dependancy
            // if it does,
                // save current dep version to a txt file
                // change its version to *
                // composer require
                // change version back using txt file
    }

    private function determineName(array $composerArray)
    {
        $composerArray = ComposerFileManager::read(
            $package->getPath()
        );

        $this->setName($composerArray['name']);
    }

    private function determineNamespace(array $composerArray)
    {
        $composerArray = ComposerFileManager::read(
            $package->getPath()
        );

        dump($composerArray, 'HERE');
    }

    private function determineDependancies(array $composerArray)
    {
        $composerArray = ComposerFileManager::read(
            $package->getPath()
        );
    }
}
