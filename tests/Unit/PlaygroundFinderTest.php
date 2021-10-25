<?php

namespace DavidPeach\Manuscript\Tests\Unit;

use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\Playground\PlaygroundFinder;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PlaygroundFinderTest extends TestCase
{
    private string $root;

    private Filesystem $fs;

    /**
     * Clear out the "tests/test-environments" directory before each new test
     */
    public function setUp(): void
    {
        $this->root = realpath(__DIR__ . '/../test-environments/playground-finder');

        $this->fs = new Filesystem;
        $this->fs->remove(
            (new Finder)->directories()->in($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY)
        );

        parent::setUp();
    }

    /** @test */
    public function it_can_discover_existing_framework_playgrounds_in_the_manuscript_playground_directory()
    {
        $this->fs->mkdir( $this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-1');
        $this->fs->mkdir( $this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-2');
        $this->fs->mkdir( $this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-3');

        $composerMock = $this->createMock(ComposerFileManager::class);
        $composerMock->method('read')->willReturn(['name' => 'manuscript/playground']);


        $existingPlaygrounds = (new PlaygroundFinder($composerMock))->discover($this->root);

        $this->assertIsArray($existingPlaygrounds);

        $this->assertArrayHasKey('playground-1', $existingPlaygrounds);
        $this->assertArrayHasKey('playground-2', $existingPlaygrounds);
        $this->assertArrayHasKey('playground-3', $existingPlaygrounds);

    }
}
