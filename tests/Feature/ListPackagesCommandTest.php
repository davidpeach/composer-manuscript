<?php

namespace DavidPeach\Manuscript\Tests\Feature;

use DavidPeach\Manuscript\Commands\ListPackagesCommand;
use DavidPeach\Manuscript\Finders\DevPackageFinder;
use DavidPeach\Manuscript\Models\DevPackageModel;
use DavidPeach\Manuscript\Tests\TestCase;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ListPackagesCommandTest extends TestCase
{
    private string $directory;

    private Filesystem $fs;

    public function setUp(): void
    {
        $this->directory = realpath(__DIR__ . '/../test-environments/commands/status/root-with-packages');
        $this->fs = new Filesystem;
        parent::setUp();
    }

    /** @test */
    public function it_can_list_all_local_packages_in_the_packages_directory()
    {
        $mockedDevPackagesFinder = $this->getMockBuilder(className: DevPackageFinder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(methods: ['discover'])
            ->getMock();

        $packageA = new DevPackageModel();
        $packageA->setName(name: 'manuscript-testing/package-a');
        $packageA->setCurrentBranch(branch: 'foo-bar/baz-branch-1');

        $packageB = new DevPackageModel();
        $packageB->setName(name: 'manuscript-testing/package-b');
        $packageB->setCurrentBranch(branch: 'foo-bar/baz-branch-2');

        $mockedDevPackagesFinder->method('discover')->willReturn(value: [
            'package-a-folder' => $packageA,
            'package-b-folder' => $packageB,
        ]);

        $command = new ListPackagesCommand($mockedDevPackagesFinder);
        $command->setHelperSet(helperSet: new HelperSet(helpers: [new QuestionHelper]));

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--dir' => $this->directory,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString(
            'manuscript-testing/package-a',
            $output
        );

        $this->assertStringContainsString(
            'foo-bar/baz-branch-1',
            $output
        );

        $this->assertStringContainsString(
            'manuscript-testing/package-b',
            $output
        );

        $this->assertStringContainsString(
            'foo-bar/baz-branch-2',
            $output
        );
    }
}
