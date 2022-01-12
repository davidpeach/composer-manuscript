<?php

namespace DavidPeach\Manuscript\Finders;

use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\ModelFactory;
use DavidPeach\Manuscript\DevPackageModelFactory;
use DavidPeach\Manuscript\PackageModelFactory;

class DevPackages extends PackageFinder
{
    public function __construct(protected PackageModelFactory $modelFactory)
    {
    }

    public function directoryToSearch(): string
    {
        return 'packages';
    }

//    protected function getModelFactory(): ModelFactory
//    {
//        return
//        return new DevPackageModelFactory(
//            composer: new ComposerFileManager
//        );
//    }
}
