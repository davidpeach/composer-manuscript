<?php

namespace DavidPeach\Manuscript\Finders;

use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\ModelFactory;
use DavidPeach\Manuscript\PlaygroundPackageModelFactory;

class PlaygroundPackages extends PackageFinder
{
    public function directoryToSearch(): string
    {
        return 'playgrounds';
    }

    protected function getModelFactory(): ModelFactory
    {
        return new PlaygroundPackageModelFactory(composer: new ComposerFileManager);
    }
}
