<?php

namespace DavidPeach\Manuscript;

class PlaygroundPackageModelFactory extends PackageModelFactory implements ModelFactory
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