<?php

namespace DavidPeach\Manuscript\Tests\Unit;

use DavidPeach\Manuscript\Config;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ConfigTest extends TestCase
{
    private string $directory;

    private Filesystem $fs;

    /**
     * Clear out the "tests/test-environments" directory before each new test
     */
    public function setUp(): void
    {
        $this->directory = realpath(__DIR__ . '/../test-environments/config-directory-test-env');

        $this->fs = new Filesystem;
        $this->fs->remove(
            (new Finder)->ignoreDotFiles(false)->in($this->directory)->files()
        );

        parent::setUp();
    }

    /** @test */
    public function it_creates_a_manuscript_config_file_with_default_values_if_not_present()
    {
        // given the target directory doesn't have a config file in
        $this->assertFalse(
            $this->fs->exists($this->directory . '/.manuscript')
        );

        // when newing up the config
        new Config($this->directory, $this->fs);

        // the config should be created with default content
        $this->assertTrue(
            $this->fs->exists($this->directory . '/.manuscript')
        );
    }

    /** @test */
    public function it_can_return_a_github_personal_access_token()
    {
        // given a manuscript config file exists
        // with the wanted key.
        file_put_contents(
            $this->directory . '/.manuscript',
            json_encode(['git_personal_access_token' => 'TEST_TOKEN']),
        );

        // when accessing that key
        $token = (new Config($this->directory, $this->fs))->gitPersonalAccessToken();

        // the correct value should be returned
        $this->assertEquals(
            'TEST_TOKEN',
            $token
        );
    }

    /** @test */
    public function it_can_update_a_github_personal_access_token_with_a_new_one()
    {
        // given a manuscript config file exists
        // and the wanted key exists
        file_put_contents(
            $this->directory . '/.manuscript',
            json_encode(['git_personal_access_token' => 'TEST_TOKEN']),
        );

        // when updating the key to a new value
        $config = new Config($this->directory, $this->fs);
        $config->updateConfig('git_personal_access_token', 'FRESH_TEST_TOKEN');

        //when accessing that key should get the new value
        $this->assertEquals(
            'FRESH_TEST_TOKEN',
            $config->gitPersonalAccessToken()
        );
    }
}
