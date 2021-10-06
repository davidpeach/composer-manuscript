<?php

namespace DavidPeach\Manuscript\Tests\Feature;

use DavidPeach\Manuscript\Commands\ManuscriptClearPlaygroundsCommand;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ManuscriptClearPlaygroundsCommandTest extends TestCase
{
    private Filesystem $fs;
    private string $directory;

    /**
     * Clear out the "tests/test-environments" directory before each new test
     */
    public function setUp(): void
    {
        $this->directory = realpath(__DIR__ . '/../test-environments/clear-command-test-env/');

        $this->fs = new Filesystem;
        $this->fs->remove($this->directory . '/manuscript-playgrounds');

        $this->fs->mkdir($this->directory . '/manuscript-playgrounds/playground-1');
        $this->fs->mkdir($this->directory . '/manuscript-playgrounds/playground-2');
        $this->fs->mkdir($this->directory . '/manuscript-playgrounds/playground-3');

        parent::setUp();
    }

    /** @test */
    public function the_playgrounds_directory_can_be_cleared_out()
    {
        $this->assertTrue(
            $this->fs->exists($this->directory . '/manuscript-playgrounds/playground-1')
        );

        $this->assertTrue(
            $this->fs->exists($this->directory . '/manuscript-playgrounds/playground-2')
        );

        $this->assertTrue(
            $this->fs->exists($this->directory . '/manuscript-playgrounds/playground-3')
        );

        $command = new ManuscriptClearPlaygroundsCommand;

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--install-dir' => $this->directory,
        ]);

        $this->assertFalse(
            $this->fs->exists($this->directory . '/manuscript-playgrounds/playground-1')
        );

        $this->assertFalse(
            $this->fs->exists($this->directory . '/manuscript-playgrounds/playground-2')
        );

        $this->assertFalse(
            $this->fs->exists($this->directory . '/manuscript-playgrounds/playground-3')
        );

    }


}
