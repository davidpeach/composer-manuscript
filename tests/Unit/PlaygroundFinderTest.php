<?php

namespace DavidPeach\Manuscript\Tests\Unit;

use DavidPeach\Manuscript\Playgrounds;
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
            (new Finder)->directories()->in($this->root . '/' . Playgrounds::PLAYGROUND_DIRECTORY)
        );

        parent::setUp();
    }

    /** @test */
    public function it_can_discover_existing_framework_playgrounds_in_the_manuscript_playground_directory()
    {
        $composerStubContents = file_get_contents(__DIR__ . '/../test-environments/stubs/composer.json');

        $this->fs->mkdir( $this->root . '/' . Playgrounds::PLAYGROUND_DIRECTORY . '/playground-1');
        file_put_contents(
            $this->root . '/' . Playgrounds::PLAYGROUND_DIRECTORY . '/playground-1/composer.json',
            $composerStubContents
        );

        $this->fs->mkdir( $this->root . '/' . Playgrounds::PLAYGROUND_DIRECTORY . '/playground-2');
        file_put_contents(
            $this->root . '/' . Playgrounds::PLAYGROUND_DIRECTORY . '/playground-2/composer.json',
            $composerStubContents
        );

        $this->fs->mkdir( $this->root . '/' . Playgrounds::PLAYGROUND_DIRECTORY . '/playground-3');
        file_put_contents(
            $this->root . '/' . Playgrounds::PLAYGROUND_DIRECTORY . '/playground-3/composer.json',
            $composerStubContents
        );

        // todo - check if needed.
//        $composerMock = $this->createMock(ComposerFileManager::class);
//        $composerMock->method('read')->willReturn(['name' => 'manuscript/playground']);
//        $this->mContainer->set('composer_file_manager', $composerMock);

        $existingPlaygrounds = (new Playgrounds(
            $this->mContainer->get('package_model_factory')
        ))->discover($this->root);

        $this->assertIsArray($existingPlaygrounds);

        $this->assertArrayHasKey('playground-1', $existingPlaygrounds);
        $this->assertArrayHasKey('playground-2', $existingPlaygrounds);
        $this->assertArrayHasKey('playground-3', $existingPlaygrounds);
    }
}
