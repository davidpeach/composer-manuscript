<?php

namespace DavidPeach\Manuscript\Finders;

class Packages extends PackageFinder
{
    public function directoryToSearch(): string
    {
        return 'packages';
    }
}
