<?php

namespace DavidPeach\Manuscript\Tests\Unit;

use DavidPeach\Manuscript\Models\Factories\PlaygroundPackageModelFactory;
use DavidPeach\Manuscript\Tests\TestCase;
use DavidPeach\Manuscript\Utilities\ComposerFileManager;

class PackageModelFactoryTest extends TestCase
{
    /** @test */
    public function it_can_create_a_model_for_a_composer_package_that_is_at_the_given_path()
    {
        // Given there is a composer package at a known path
        $pathToPackage = __DIR__ . '/../test-environments/package-model-factory-test/test-package';

        // When passing that path to the PackageModelFactory
        $packageModel = (new PlaygroundPackageModelFactory(new ComposerFileManager))->fromPath($pathToPackage);

        // Then I should get back a model that represents the package at that path
        $this->assertEquals(
            'manuscript/model-factory-unit-test',
            $packageModel->getName()
        );

        $this->assertEquals(
            $pathToPackage,
            $packageModel->getPath()
        );

        $this->assertEquals(
            'test-package',
            $packageModel->getFolderName()
        );
    }
}





