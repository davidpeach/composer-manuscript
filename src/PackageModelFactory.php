<?php

namespace DavidPeach\Manuscript;

class PackageModelFactory
{
    public function __construct(
        private ComposerFileManager $composer
    ){}

    public function fromPath(string $pathToPackage): PackageModel
    {
        $composerData = $this->composer->read($pathToPackage);

        $packageModel = new PackageModel;

        $packageModel->setName($composerData['name']);

        $packageModel->setPath($pathToPackage);

        $pathParts = explode(DIRECTORY_SEPARATOR, $pathToPackage);
        $packageModel->setFolderName(end($pathParts));

        return $packageModel;
    }
}
