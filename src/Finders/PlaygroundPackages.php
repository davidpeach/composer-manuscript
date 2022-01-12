<?php

namespace DavidPeach\Manuscript\Finders;

use DavidPeach\Manuscript\PackageModelFactory;
use DavidPeach\Manuscript\PlaygroundPackageModelFactory;

class PlaygroundPackages extends PackageFinder
{
    public function __construct(protected PackageModelFactory $modelFactory)
    {
    }

    public function directoryToSearch(): string
    {
        return 'playgrounds';
    }

//    protected function getModelFactory(): ModelFactory
//    {
//        return new PlaygroundPackageModelFactory(composer: new ComposerFileManager);
//    }
}
