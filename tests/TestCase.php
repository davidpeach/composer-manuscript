<?php

namespace DavidPeach\Manuscript\Tests;

use DavidPeach\Manuscript\Finders\Packages;
use DavidPeach\Manuscript\Finders\Playgrounds;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestCase extends KernelTestCase
{
    protected Packages $packageFinder;

    protected Playgrounds $playgroundFinder;

    public function setUp(): void
    {
        parent::setUp();

        $this->packageFinder = new Packages();

        $this->playgroundFinder = new Playgrounds();
    }
}
