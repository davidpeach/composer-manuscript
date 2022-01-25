<?php

namespace DavidPeach\Manuscript\Tests\Unit;

use DavidPeach\Manuscript\Tests\TestCase;
use DavidPeach\Manuscript\Utilities\ComposerFileManager;
use Symfony\Component\Filesystem\Filesystem;

class ComposerFileManagerTest extends TestCase
{
    private string $root;

    private Filesystem $fs;

    /**
     * Clear out the "tests/test-environments" directory before each new test
     */
    public function setUp(): void
    {
        $this->root = realpath(__DIR__ . '/../test-environments/composer-file-manager');

        file_put_contents(
            $this->root . '/composer.json',
            file_get_contents(__DIR__ . '/../test-environments/stubs/composer.json')
        );

        $this->fs = new Filesystem;

        parent::setUp();
    }

    /** @test */
    public function packages_cannot_be_added_to_a_composer_require_multiple_times()
    {
        $composer = new ComposerFileManager();

        $composer->add(
            pathToFile: $this->root . '/composer.json',
            toAdd: ['repositories' => [
                [
                    'type' => 'path',
                    'url'  =>  '/some/local/path',
                    'options' => [
                        'symlink' => true,
                    ],
                ]
            ]]
        );

        $composerFileData = $composer->read($this->root . '/composer.json');

        $this->assertCount(1, $composerFileData['repositories']);

        $this->fs->remove($this->root . '/composer.json');
    }
}
