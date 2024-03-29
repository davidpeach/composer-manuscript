<?php

namespace DavidPeach\Manuscript\Tests\Feature;

use Carbon\Carbon;
use DavidPeach\Manuscript\Commands\PlayCommand;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class PlayCommandTest extends TestCase
{
    private Filesystem $fs;
    private string $directory;

    /**
     * Clear out the "tests/test-environments" directory before each new test
     */
    public function setUp(): void
    {
        $this->directory = realpath(__DIR__ . '/../test-environments/commands/play');

        $this->fs = new Filesystem;
        $this->fs->remove($this->directory . '/playgrounds');

        parent::setUp();
    }

    /** @test */
    public function it_installs_a_laravel_playground_and_installs_the_package_into_the_playground_with_path_symlink()
    {
        $command = $this->getCommand(command: 'play_command');
        $command->setHelperSet(new HelperSet([new QuestionHelper]));

        Carbon::setTestNow('29th August 1997');

        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            'laravel10x',
        ]);

        $commandTester->execute([
            '--dir' => $this->directory . '/packages/test-package',
        ]);

        $this->assertTrue(
            $this->fs->exists($this->directory . '/playgrounds')
        );

        $this->assertTrue(
            $this->fs->exists($this->directory . '/playgrounds/laravel-10-872812800')
        );

        $composerFile = $this->directory . '/playgrounds/laravel-10-872812800/composer.json';
        $composerFileArray = json_decode(file_get_contents($composerFile), true);
        $this->assertEquals('dev', $composerFileArray['minimum-stability']);
        $this->assertArrayHasKey('manuscript-test/test-package', $composerFileArray['require']);

        $this->assertArrayHasKey('repositories', $composerFileArray);

        $this->assertArrayHasKey('type', $composerFileArray['repositories'][0]);
        $this->assertEquals('path', $composerFileArray['repositories'][0]['type']);

        $this->assertArrayHasKey('url', $composerFileArray['repositories'][0]);
        $this->assertEquals($this->directory . '/packages/test-package', $composerFileArray['repositories'][0]['url']);

        $this->assertArrayHasKey('options', $composerFileArray['repositories'][0]);
        $this->assertArrayHasKey('symlink', $composerFileArray['repositories'][0]['options']);
        $this->assertTrue($composerFileArray['repositories'][0]['options']['symlink']);

        $this->assertTrue(
            $this->fs->exists(
                $this->directory . '/playgrounds/laravel-10-872812800/vendor/manuscript-test/test-package'
            )
        );

        // Remove the playgrounds folder to stop PHPStorm from indexing it.
        $this->fs->remove($this->directory . '/playgrounds');
    }

    /** @test */
    public function it_wont_attempt_to_execute_the_play_command_if_not_run_in_a_composer_package()
    {
        $invalidPackage = $this->directory . '/packages/invalid-package';

        $command = $this->getCommand(command: 'play_command');
        $command->setHelperSet(new HelperSet([new QuestionHelper]));

        Carbon::setTestNow('29th August 1997');

        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            'laravel8x',
        ]);

        $commandTester->execute([
            '--dir' => $invalidPackage,
        ]);

        $this->assertEquals(
            Command::INVALID,
            $commandTester->getStatusCode()
        );

        $this->assertStringContainsString(
            'Not a valid composer package. No action taken.',
            $commandTester->getDisplay()
        );
    }
}
