<?php

namespace DavidPeach\Manuscript\Tests\Unit;

use DavidPeach\Manuscript\Models\PlaygroundPackageModel;
use DavidPeach\Manuscript\Tests\TestCase;

class PackageModelTest extends TestCase
{
    /** @test */
    public function it_can_return_its_name()
    {
        $packageModel = new PlaygroundPackageModel();
        $packageModel->setName('My Package Name');

        $this->assertEquals(
            'My Package Name',
            $packageModel->getName()
        );
    }

    /** @test */
    public function it_can_return_its_path()
    {
        $packageModel = new PlaygroundPackageModel;
        $packageModel->setPath('path/to/package');

        $this->assertEquals(
            'path/to/package',
            $packageModel->getPath()
        );
    }

    /** @test */
    public function it_can_return_its_folder_name()
    {
        $packageModel = new PlaygroundPackageModel;
        $packageModel->setFolderName('package-folder');

        $this->assertEquals(
            'package-folder',
            $packageModel->getFolderName()
        );
    }
}
