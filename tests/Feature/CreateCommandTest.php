<?php

namespace DavidPeach\Manuscript\Tests\Feature;

use DavidPeach\Manuscript\Commands\CreateCommand;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class CreateCommandTest extends TestCase
{
    private Filesystem $fs;
    private string $directory;

    /**
     * Clear out the "tests/test-environments" directory before each new test
     */
    public function setUp(): void
    {
        $this->directory = realpath(__DIR__ . '/../test-environments/commands/create');

        $this->fs = new Filesystem;
        $this->fs->remove(
            (new Finder)->directories()->in($this->directory . '/packages')
        );

        parent::setUp();
    }

    /** @test */
    public function it_generates_a_new_bare_bones_composer_package()
    {
        $command = new CreateCommand;
        $command->setHelperSet(new HelperSet([new QuestionHelper]));

        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            'manuscript-test/package-name',
            'This is the manuscript test package',
            'David Peach',
            'test@example.com',
            'stable',
            'MIT',
        ]);

        $commandTester->execute([
            '--install-dir' => $this->directory . '/packages',
        ]);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode()
        );

        $this->assertTrue(
            $this->fs->exists($this->directory . '/packages/package-name/composer.json')
        );

        $composerArray = json_decode(
            file_get_contents($this->directory . '/packages/package-name/composer.json'),
            true
        );

        $this->assertEquals(
            'manuscript-test/package-name',
            $composerArray['name']
        );

        $this->assertEquals(
            'This is the manuscript test package',
            $composerArray['description']
        );

        $this->assertEquals(
            'David Peach',
            $composerArray['authors'][0]['name']
        );

        $this->assertEquals(
            'test@example.com',
            $composerArray['authors'][0]['email']
        );

        $this->assertEquals(
            'stable',
            $composerArray['minimum-stability']
        );

        $this->assertEquals(
            'MIT',
            $composerArray['license']
        );

        $this->assertArrayHasKey(
            'ManuscriptTest\\PackageName\\',
            $composerArray['autoload']['psr-4']
        );

        $this->assertEquals(
            'src/',
            $composerArray['autoload']['psr-4']['ManuscriptTest\\PackageName\\']
        );

        $this->assertTrue(
            $this->fs->exists($this->directory . '/packages/package-name/src')
        );
    }

    /** @test */
    public function it_wont_generate_a_package_if_the_folder_name_already_exists()
    {
        // Create the expected folder before running command.
        $this->fs->mkdir($this->directory . '/packages/package-name');

        $command = new CreateCommand;
        $command->setHelperSet(new HelperSet([new QuestionHelper]));

        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            'manuscript-test/package-name',
            'This is the manuscript test package',
            'David Peach',
            'test@example.com',
            'stable',
            'MIT',
        ]);

        $commandTester->execute([
            '--install-dir' => $this->directory . '/packages',
        ]);

        $this->assertEquals(
            Command::FAILURE,
            $commandTester->getStatusCode()
        );

        // The composer file shouldn't be there as the folder already existed.
        $this->assertFalse(
            $this->fs->exists($this->directory . '/packages/package-name/composer.json')
        );
    }
}
