<?php

namespace DavidPeach\Manuscript\PackageBuilders;

interface PackageBuilderContract
{
    /**
     * Builds a package and returns the fully-qualified path
     * to that new package.
     * @return string
     */
    public function build(): string;

    public function setRoot(string $root): self;
}
