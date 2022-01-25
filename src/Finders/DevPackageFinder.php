<?php

namespace DavidPeach\Manuscript\Finders;

use DavidPeach\Manuscript\Models\Factories\PackageModelFactory;

class DevPackageFinder extends PackageFinder
{
    public function __construct(protected PackageModelFactory $modelFactory)
    {
    }

    public function directoryToSearch(): string
    {
        return 'packages';
    }
}
