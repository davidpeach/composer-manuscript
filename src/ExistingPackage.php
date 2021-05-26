<?php

namespace Davidpeach\Manuscript;

class ExistingPackage extends Package
{
    public function getDirectory()
    {
        return getcwd();
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getData()
    {
        $composerArray = json_decode(file_get_contents($this->getDirectory() . '/composer.json'), true);
        $this->data['name'] = $composerArray['name'];
    }
}
