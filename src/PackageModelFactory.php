<?php

namespace DavidPeach\Manuscript;

use DavidPeach\Manuscript\Exceptions\ComposerFileNotFoundException;
use DavidPeach\Manuscript\Exceptions\PackageModelNotCreatedException;

abstract class PackageModelFactory
{
    protected PackageModel $packageModel;

    public function __construct(
        protected ComposerFileManager $composer
    ){}

    /**
     * @param string $pathToPackage
     * @return PackageModel
     * @throws PackageModelNotCreatedException
     */
    public function fromPath(string $pathToPackage): PackageModel
    {
        try {
            $composerData = $this->composer->read(pathToFile: $pathToPackage);

            $this->packageModel = $this->getPackageModel();

            $this->packageModel->setName(name: $composerData['name']);

            $this->packageModel->setPath(path: $pathToPackage);

            $pathParts = explode(separator: DIRECTORY_SEPARATOR, string: $pathToPackage);
            $this->packageModel->setFolderName(folderName: end($pathParts));

            $this->setAdditionalPackageAttributes();

            return $this->packageModel;
        } catch (ComposerFileNotFoundException $e) {
            throw new PackageModelNotCreatedException(
                message: 'Not a valid composer package. No action taken.',
                code: $e->getCode(),
                previous: $e
            );
        }
    }

    abstract protected function getPackageModel(): PackageModel;

    abstract protected function setAdditionalPackageAttributes(): void;
}