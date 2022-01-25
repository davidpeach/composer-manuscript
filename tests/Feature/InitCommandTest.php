<?php

namespace DavidPeach\Manuscript\Tests\Feature;

use DavidPeach\Manuscript\Commands\InitCommand;
use DavidPeach\Manuscript\Scratch\MyClass;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class InitCommandTest extends TestCase
{
    private string $directory;

    private Filesystem $fs;

    public function setUp(): void
    {
        $this->directory = realpath(__DIR__ . '/../test-environments/commands/init');
        $this->fs = new Filesystem;
        parent::setUp();
    }

    public function it_can_mock_something()
    {
        $mock = $this->createPartialMock(MyClass::class, [
            'doSomething',
        ]);

        $mock->expects($this->once())
            ->method('doSomething')
            ->willReturn('THE TEST IMPLEMENTATION');

        $this->mContainer->set('my_class', $mock);

        $command = new InitCommand($this->mContainer);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--dir' => $this->directory . '/empty',
        ]);
    }

    /** @test */
    public function it_initialises_an_empty_directory_as_a_manuscript_directory()
    {
        $directory = $this->directory . '/empty';

        // Delete any testable files from previous tests
        $this->fs->remove($directory . '/playgrounds');
        $this->fs->remove($directory . '/packages');
        $this->fs->remove($directory . '/.manuscript');

        $command = $this->getCommand(command: 'init_command');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--dir' => $directory,
        ]);

        $this->assertTrue(
            $this->fs->exists($directory . '/playgrounds')
        );

        $this->assertTrue(
            $this->fs->exists($directory . '/packages')
        );

        $this->assertTrue(
            $this->fs->exists($directory . '/.manuscript')
        );
    }

    /** @test */
    public function it_will_tell_you_when_a_file_already_exists()
    {
        $directory = $this->directory . '/existing';

        $command = $this->getCommand(command: 'init_command');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--dir' => $directory,
        ]);

        $output = $commandTester->getDisplay();

        // Messages to sow that directories already existing
        $this->assertStringContainsString(
            'Playgrounds directory already exists. No action taken.',
            $output
        );
        $this->assertStringContainsString(
            'Packages directory already exists. No action taken.',
            $output
        );

        // Assert that the .manuscript file is the existing test one
        // That it hasn't been re-initialized.
        $this->assertSame(
            ['init_test' => 'existing_test_config'],
            json_decode(
                file_get_contents($directory . '/.manuscript'),
                true
            ));

    }
}
