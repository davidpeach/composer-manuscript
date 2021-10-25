<?php

namespace DavidPeach\Manuscript;

class ExistingPackage extends Package
{
    public function getData(): self
    {
        $composerArray = $this->composerFileManager->read($this->getPath());
        $this->name = $composerArray['name'];

        return $this;
    }
}
