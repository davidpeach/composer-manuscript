<?php

namespace DavidPeach\Manuscript\Finders;

class Playgrounds extends PackageFinder
{
    public function directoryToSearch(): string
    {
        return 'playgrounds';
    }
}
