<?php

namespace DavidPeach\Manuscript\Frameworks;

use Illuminate\Support\Str;

abstract class Framework
{
    public function getName(): string
    {
        return $this->name;
    }

    public function getInstallCommandSegment(): string
    {
        return $this->installCommmandSegment;
    }

    public function folderFormat(): string
    {
        return  Str::slug($this->getName());
    }
}
