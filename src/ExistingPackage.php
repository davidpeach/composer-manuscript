<?php

namespace DavidPeach\Manuscript;

class ExistingPackage extends Package
{
    public function getData(): self
    {
        $composerArray = ComposerFileManager::read($this->getPath() . '/composer.json');
        $this->name = $composerArray['name'];

        return $this;
    }
}
