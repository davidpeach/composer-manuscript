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
    }
}
