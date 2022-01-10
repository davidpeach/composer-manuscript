<?php

namespace DavidPeach\Manuscript\Tests;

use DavidPeach\Manuscript\Finders\DevPackages;
use DavidPeach\Manuscript\Finders\PlaygroundPackages;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestCase extends KernelTestCase
{
    protected DevPackages $packageFinder;

    protected PlaygroundPackages $playgroundFinder;

    public function setUp(): void
    {
        parent::setUp();

        $this->packageFinder = new DevPackages();

        $this->playgroundFinder = new PlaygroundPackages();
    }
}
