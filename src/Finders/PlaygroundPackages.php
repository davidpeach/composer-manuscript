<?php

namespace DavidPeach\Manuscript\Finders;

use DavidPeach\Manuscript\PackageModelFactory;

class PlaygroundPackages extends PackageFinder
{
    public function __construct(protected PackageModelFactory $modelFactory)
    {
    }

    public function directoryToSearch(): string
    {
        return 'playgrounds';
    }
}
