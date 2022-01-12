<?php

namespace DavidPeach\Manuscript\Tests;

use DavidPeach\Manuscript\Finders\DevPackages;
use DavidPeach\Manuscript\Finders\PlaygroundPackages;
use DavidPeach\Manuscript\Commands\ClearPlaygroundsCommand;
use DavidPeach\Manuscript\Commands\CreateCommand;
use DavidPeach\Manuscript\Commands\InitCommand;
use DavidPeach\Manuscript\Commands\PlayCommand;
use DavidPeach\Manuscript\Commands\StatusCommand;
use DavidPeach\Manuscript\Container\Container;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestCase extends KernelTestCase
{
    protected DevPackages $packageFinder;

    protected PlaygroundPackages $playgroundFinder;

    protected Container $mContainer;


    public function setUp(): void
    {
        parent::setUp();

        $this->mContainer = new Container();

        $this->packageFinder = $this->mContainer->get(id: 'dev_packages_finder');

        $this->playgroundFinder = $this->mContainer->get(id: 'playground_packages_finder');
    }

    protected function getCommand(string $command)
    {
        return $this->mContainer->get($command);
    }
}

