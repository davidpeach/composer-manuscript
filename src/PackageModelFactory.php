<?php

namespace DavidPeach\Manuscript;

use DavidPeach\Manuscript\Exceptions\ComposerFileNotFoundException;
use DavidPeach\Manuscript\Exceptions\PackageModelNotCreatedException;

class PackageModelFactory
{
    public function __construct(
        private ComposerFileManager $composer
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

            $packageModel = new PackageModel;

            $packageModel->setName(name: $composerData['name']);

            $packageModel->setPath(path: $pathToPackage);

            $pathParts = explode(separator: DIRECTORY_SEPARATOR, string: $pathToPackage);
            $packageModel->setFolderName(folderName: end($pathParts));

            return $packageModel;
        } catch (ComposerFileNotFoundException $e) {
            throw new PackageModelNotCreatedException(
                message: 'Not a valid composer package. No action taken.',
                code: $e->getCode(),
                previous: $e
            );
        }
    }
}
