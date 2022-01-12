<?php

namespace DavidPeach\Manuscript;

class PlaygroundPackageModelFactory extends PackageModelFactory
{
    protected function getPackageModel(): PlaygroundPackageModel
    {
        return new PlaygroundPackageModel();
    }

    protected function setAdditionalPackageAttributes(): void
    {
        //
    }
}