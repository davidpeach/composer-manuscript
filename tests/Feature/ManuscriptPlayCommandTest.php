<?php

namespace DavidPeach\Manuscript\Tests\Feature;

use Carbon\Carbon;
use DavidPeach\Manuscript\Commands\ManuscriptPlayCommand;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ManuscriptPlayCommandTest extends TestCase
{
    private Filesystem $fs;
    private string $directory;

    /**
     * Clear out the "tests/test-environments" directory before each new test
     */
    public function setUp(): void
    {
        $this->directory = realpath(__DIR__ . '/../test-environments/play-command-test-env');

        $this->fs = new Filesystem;
        $this->fs->remove($this->directory . '/manuscript-playgrounds');

        parent::setUp();
    }

    /** @test */
    public function it_installs_a_laravel_playground_and_installs_the_package_into_the_playground_with_path_symlink()
    {
        $command = new ManuscriptPlayCommand;
        $command->setHelperSet(new HelperSet([new QuestionHelper]));

        Carbon::setTestNow('29th August 1997');

        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            'laravel8x',
        ]);

        $commandTester->execute([
            '--package-dir' => $this->directory . '/test-package',
        ]);

        $this->assertTrue(
            $this->fs->exists($this->directory . '/manuscript-playgrounds')
        );

        $this->assertTrue(
            $this->fs->exists($this->directory . '/manuscript-playgrounds/laravel-8-' . Carbon::now()->timestamp)
        );

        $composerFile = $this->directory . '/manuscript-playgrounds/laravel-8-' . Carbon::now()->timestamp . '/composer.json';
        $composerFileArray = json_decode(file_get_contents($composerFile), true);
        $this->assertArrayHasKey('manuscript-test/test-package', $composerFileArray['require']);

        $this->assertArrayHasKey('repositories', $composerFileArray);

        $this->assertArrayHasKey('type', $composerFileArray['repositories'][0]);
        $this->assertEquals('path', $composerFileArray['repositories'][0]['type']);

        $this->assertArrayHasKey('url', $composerFileArray['repositories'][0]);
        $this->assertEquals($this->directory . '/test-package', $composerFileArray['repositories'][0]['url']);

        $this->assertArrayHasKey('options', $composerFileArray['repositories'][0]);
        $this->assertArrayHasKey('symlink', $composerFileArray['repositories'][0]['options']);
        $this->assertTrue($composerFileArray['repositories'][0]['options']['symlink']);

        $this->assertTrue(
            $this->fs->exists(
                $this->directory . '/manuscript-playgrounds/laravel-8-' . Carbon::now()->timestamp . '/vendor/manuscript-test/test-package'
            )
        );
    }
}
