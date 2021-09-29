<?php

namespace DavidPeach\Manuscript;

class ExistingPackage extends Package
{
    public function getPath(): string
    {
        return $this->directory;
    }

    public function getData(): void
    {
        $composerArray = ComposerFileManager::read($this->getPath() . '/composer.json');
        $this->name = $composerArray['name'];
    }
}
