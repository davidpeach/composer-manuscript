<?php

namespace Davidpeach\Manuscript\Frameworks;

use Illuminate\Support\Str;

abstract class Framework
{
    public function getName(): string
    {
        return $this->name;
    }

    public function getInstallCommmandSegment(): string
    {
        return $this->installCommmandSegment;
    }

    public function folderFormat(): string
    {
        return  Str::slug($this->getName());
    }
}
