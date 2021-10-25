<?php

namespace DavidPeach\Manuscript\Tests\Feature;

use DavidPeach\Manuscript\Commands\ManuscriptClearPlaygroundsCommand;
use DavidPeach\Manuscript\ComposerFileManager;
use DavidPeach\Manuscript\Playground\PlaygroundFinder;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ManuscriptClearPlaygroundsCommandTest extends TestCase
{
    private Filesystem $fs;
    private string $root;

    /**
     * Clear out the "tests/test-environments" directory before each new test
     */
    public function setUp(): void
    {
        $this->root = realpath(__DIR__ . '/../test-environments/clear-command-test-env/');

        $this->fs = new Filesystem;
        $this->fs->remove($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY);

        $composerStubContents = file_get_contents(__DIR__ . '/../test-environments/stubs/composer.json');

        $this->fs->mkdir($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-1');
        file_put_contents(
            $this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-1/composer.json',
            $composerStubContents
        );

        $this->fs->mkdir($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-2');
        file_put_contents(
            $this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-2/composer.json',
            $composerStubContents
        );

        $this->fs->mkdir($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-3');
        file_put_contents(
            $this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-3/composer.json',
            $composerStubContents
        );

        parent::setUp();
    }

    /** @test */
    public function the_playgrounds_directory_can_be_cleared_out()
    {
        $this->assertTrue(
            $this->fs->exists($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-1')
        );

        $this->assertTrue(
            $this->fs->exists($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-2')
        );

        $this->assertTrue(
            $this->fs->exists($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-3')
        );

        $composerMock = $this->createMock(ComposerFileManager::class);
        $composerMock->method('read')->willReturn(['name' => 'manuscript/playground']);

        $command = new ManuscriptClearPlaygroundsCommand;

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--install-dir' => $this->root,
        ]);

        $this->assertFalse(
            $this->fs->exists($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-1')
        );

        $this->assertFalse(
            $this->fs->exists($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-2')
        );

        $this->assertFalse(
            $this->fs->exists($this->root . '/' . PlaygroundFinder::PLAYGROUND_DIRECTORY . '/playground-3')
        );

    }


}
