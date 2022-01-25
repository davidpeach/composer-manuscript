<?php

namespace DavidPeach\Manuscript\Models\Factories;

use DavidPeach\Manuscript\Models\PlaygroundPackageModel;

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