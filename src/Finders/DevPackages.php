<?php

namespace DavidPeach\Manuscript\Finders;

use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\ModelFactory;
use DavidPeach\Manuscript\DevPackageModelFactory;

class DevPackages extends PackageFinder
{
    public function directoryToSearch(): string
    {
        return 'packages';
    }

    protected function getModelFactory(): ModelFactory
    {
        return new DevPackageModelFactory(
            composer: new ComposerFileManager
        );
    }
}
