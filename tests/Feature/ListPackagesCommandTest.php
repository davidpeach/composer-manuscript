<?php

namespace DavidPeach\Manuscript\Tests\Feature;

use DavidPeach\Manuscript\Commands\ListPackagesCommand;
use DavidPeach\Manuscript\DevPackageModelFactory;
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
//        $mock = $this->createPartialMock(DevPackageModelFactory::class, [
//            'determineCurrentBranch',
//        ]);
//        $mock->expects($this->once())
//            ->method('determineCurrentBranch')
//            ->willReturn('foo/branch');


        $command = $this->getCommand(command: 'list_packages_command');
        $command->setHelperSet(new HelperSet([new QuestionHelper]));

        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--dir' => $this->directory,
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString(
            'manuscript-testing/package-one',
            $output
        );

        $this->assertStringContainsString(
            'manuscript-testing/package-two',
            $output
        );
    }
}
