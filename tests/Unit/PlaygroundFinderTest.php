<?php

namespace DavidPeach\Manuscript\Tests\Unit;

use DavidPeach\Manuscript\Playground\PlaygroundFinder;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PlaygroundFinderTest extends TestCase
{
    private string $directory;

    private Filesystem $fs;

    /**
     * Clear out the "tests/test-environments" directory before each new test
     */
    public function setUp(): void
    {
        $this->directory = realpath(__DIR__ . '/../test-environments/playground-finder');

        $this->fs = new Filesystem;
        $this->fs->remove(
            (new Finder)->directories()->in($this->directory)
        );

        parent::setUp();
    }

    /** @test */
    public function it_can_discover_existing_framework_playgrounds_in_the_manuscript_playground_directory()
    {
        $this->fs->mkdir( $this->directory . '/playground-1');
        $this->fs->mkdir( $this->directory . '/playground-2');
        $this->fs->mkdir( $this->directory . '/playground-3');

        $existingPlaygrounds = (new PlaygroundFinder)->discover($this->directory);

        $this->assertIsArray($existingPlaygrounds);

        $this->assertArrayHasKey('playground-1', $existingPlaygrounds);
        $this->assertArrayHasKey('playground-2', $existingPlaygrounds);
        $this->assertArrayHasKey('playground-3', $existingPlaygrounds);

    }
}
