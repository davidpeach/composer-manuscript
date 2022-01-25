<?php

namespace DavidPeach\Manuscript\Tests\Feature;

use DavidPeach\Manuscript\Tests\TestCase;
use DavidPeach\Manuscript\Utilities\ComposerFileManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ClearPlaygroundsCommandTest extends TestCase
{
    private Filesystem $fs;
    private string $root;

    /**
     * Clear out the "tests/test-environments" directory before each new test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->root = realpath(__DIR__ . '/../test-environments/commands/clear');

        $this->fs = new Filesystem;
        $this->fs->remove($this->root . '/valid/' . $this->playgroundFinder->directoryToSearch());

        $composerStubContents = file_get_contents(__DIR__ . '/../test-environments/stubs/composer.json');

        $this->fs->mkdir($this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-1');
        file_put_contents(
            $this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-1/composer.json',
            $composerStubContents
        );

        $this->fs->mkdir($this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-2');
        file_put_contents(
            $this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-2/composer.json',
            $composerStubContents
        );

        $this->fs->mkdir($this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-3');
        file_put_contents(
            $this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-3/composer.json',
            $composerStubContents
        );
    }

    /** @test */
    public function the_playgrounds_directory_can_be_cleared_out()
    {
        $this->assertTrue(
            $this->fs->exists($this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-1')
        );

        $this->assertTrue(
            $this->fs->exists($this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-2')
        );

        $this->assertTrue(
            $this->fs->exists($this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-3')
        );

        $composerMock = $this->createMock(ComposerFileManager::class);
        $composerMock->method('read')->willReturn(['name' => 'manuscript/playground']);

        $command = $this->getCommand(command: 'clear_command');
        $command->setHelperSet(new HelperSet([new QuestionHelper]));

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--dir' => $this->root . '/valid',
        ]);

        $this->assertFalse(
            $this->fs->exists($this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-1')
        );

        $this->assertFalse(
            $this->fs->exists($this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-2')
        );

        $this->assertFalse(
            $this->fs->exists($this->root . '/valid/' . $this->playgroundFinder->directoryToSearch() . '/playground-3')
        );

    }

    /** @test */
    public function it_wont_attempt_to_clear_playgrounds_if_not_ran_from_inside_a_manuscript_root()
    {
        $root = realpath(__DIR__ . '/../test-environments/commands/clear');


        $command = $this->getCommand(command: 'clear_command');
        $command->setHelperSet(new HelperSet([new QuestionHelper]));
        $commandTester = new CommandTester($command);

        $this->expectException(LogicException::class);

        $commandTester->execute([
            '--dir' => $root . '/invalid',
        ], ['capture_stderr_separately' => true]);

        $this->assertEquals(
            Command::INVALID,
            $commandTester->getStatusCode()
        );

        $this->assertStringContainsString(
            'Not a manuscript directory. No action taken.',
            $commandTester->getDisplay()
        );
    }


}
