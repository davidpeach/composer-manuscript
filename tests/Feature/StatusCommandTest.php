<?php

namespace DavidPeach\Manuscript\Tests\Feature;

use DavidPeach\Manuscript\Commands\StatusCommand;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class StatusCommandTest extends TestCase
{
    private string $root;

    private Filesystem $fs;

    public function setUp(): void
    {
        $this->root = realpath(__DIR__ . '/../test-environments/commands/status');
        $this->fs = new Filesystem;
        parent::setUp();
    }

    /** @test */
        public function it_displays_current_playgrounds_installed()
    {
        $directory = $this->root . '/root-with-playgrounds';

        $command = new StatusCommand;
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--dir' => $directory,
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(
            'manuscript-testing/playground-one',
            $output
        );
        $this->assertStringContainsString(
            'manuscript-testing/playground-two',
            $output
        );
    }
}
