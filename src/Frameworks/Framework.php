<?php

namespace DavidPeach\Manuscript\Frameworks;

use Illuminate\Support\Str;

abstract class Framework
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getInstallCommandSegment(): string
    {
        return $this->installCommandSegment;
    }

    /**
     * @return string
     */
    public function folderFormat(): string
    {
        return  Str::slug(title: $this->getName());
    }
}
