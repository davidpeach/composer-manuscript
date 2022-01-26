<?php

namespace DavidPeach\Manuscript\Tests\Unit\PackageBuilders;

use DavidPeach\Manuscript\Github\GithubRepository;
use DavidPeach\Manuscript\PackageBuilders\LaravelPackageBuilder;
use DavidPeach\Manuscript\Tests\TestCase;
use DavidPeach\Manuscript\Utilities\GitCredentials;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class LaravelPackageBuilderTest extends TestCase
{
    private string $directory;

    public function setUp(): void
    {
        $this->directory = realpath(__DIR__ . '/../../test-environments/laravel-package-builder');

        $this->fs = new Filesystem;
        $this->fs->remove(
            (new Finder)->directories()->in($this->directory)
        );

        $this->fs->mirror(
            originDir: __DIR__ . '/../../test-environments/stubs/laravel-package',
            targetDir: $this->directory . '/laravel-package',
            options: [
                'override' => true,
            ],
        );

        parent::setUp();
    }

    /** @test */
    public function it_generates_a_new_laravel_composer_package()
    {
        $mockedGithubRepository = $this->createPartialMock(originalClassName: GithubRepository::class, methods: [
            'clone',
            'getLocalDirectory',
        ]);

        $mockedGithubRepository->expects($this->once())
            ->method(constraint: 'clone')
            ->willReturnSelf();

        $mockedGithubRepository->expects($this->atLeastOnce())
            ->method(constraint: 'getLocalDirectory')
            ->willReturn($this->directory . '/laravel-package');

        $mockedIO = $this->getMockBuilder(SymfonyStyle::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockedIO->expects($this->exactly(7))
            ->method('ask')
            ->willReturn(
                'laravel-package',
                'manuscript',
                'a testing description',
                'test name',
                'email@test.test',
                '8.1',
                'TestNamespace',
            );

        $builder = new LaravelPackageBuilder(new GitCredentials(), $mockedGithubRepository);
        $builder->setIO($mockedIO);
        $builder->setRoot(root: $this->directory);

        $builder->build();

        $this->assertTrue(
            $this->fs->exists($this->directory . '/laravel-package/composer.json')
        );

        $composerArray = json_decode(
            file_get_contents($this->directory . '/laravel-package/composer.json'),
            true
        );

        $this->assertEquals(
            'manuscript/laravel-package',
            $composerArray['name']
        );

        $this->assertEquals(
            'a testing description',
            $composerArray['description']
        );

        $this->assertEquals(
            'test name',
            $composerArray['authors'][0]['name']
        );

        $this->assertEquals(
            'email@test.test',
            $composerArray['authors'][0]['email']
        );

//        $this->assertEquals(
//            'stable',
//            $composerArray['minimum-stability']
//        );

//        $this->assertEquals(
//            'MIT',
//            $composerArray['license']
//        );

//        $this->assertArrayHasKey(
//            'ManuscriptTest\\PackageName\\',
//            $composerArray['autoload']['psr-4']
//        );

//        $this->assertEquals(
//            'src/',
//            $composerArray['autoload']['psr-4']['ManuscriptTest\\PackageName\\']
//        );
//
//        $this->assertTrue(
//            $this->fs->exists($this->directory . '/valid/packages/package-name/src')
//        );

    }
}