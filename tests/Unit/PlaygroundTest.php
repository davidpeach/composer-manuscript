<?php

namespace DavidPeach\Manuscript\Tests\Unit;

use DavidPeach\Manuscript\Playground\Playground;
use DavidPeach\Manuscript\Tests\TestCase;

class PlaygroundTest extends TestCase
{
    /** @test */
    public function it_can_return_its_full_package_name()
    {
        $playground = new Playground();
        $playground->setName('manuscript-test/playground');
        $this->assertEquals('manuscript-test/playground', $playground->getName());
    }
}
