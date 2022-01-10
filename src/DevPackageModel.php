<?php

namespace DavidPeach\Manuscript;

class DevPackageModel extends PackageModel
{
    private string $branch = 'branch_unknown';

    public function setCurrentBranch(string $branch): void
    {
        $this->branch = $branch;
    }

    public function getCurrentBranch(): string
    {
        return $this->branch;
    }
}