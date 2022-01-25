<?php

namespace DavidPeach\Manuscript\Models\Factories;

use DavidPeach\Manuscript\Models\DevPackageModel;
use Symfony\Component\Process\Process;

class DevPackageModelFactory extends PackageModelFactory
{
    protected function getPackageModel(): DevPackageModel
    {
        return new DevPackageModel();
    }

    protected function setAdditionalPackageAttributes(): void
    {
        if (! $this->packageModel instanceof DevPackageModel) {
            return;
        }

        $this->packageModel->setCurrentBranch(
            branch: $this->determineCurrentBranch()
        );

    }

    public function determineCurrentBranch(): string
    {
        $commands = [
            'cd ' . $this->packageModel->getPath(),
            'git rev-parse --abbrev-ref HEAD',
        ];

        $process = Process::fromShellCommandline(implode(separator: ' && ', array: $commands));

        $process->setTimeout(timeout: 3600);
        $process->run();

        if ($process->isSuccessful()) {
            return trim(string: $process->getOutput(), characters: "\n");
        }

        return 'unknown_branch_2';
    }
}
