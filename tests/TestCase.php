<?php

namespace DavidPeach\Manuscript\Tests;

use DavidPeach\Manuscript\Commands\ClearPlaygroundsCommand;
use DavidPeach\Manuscript\Commands\CreateCommand;
use DavidPeach\Manuscript\Commands\InitCommand;
use DavidPeach\Manuscript\Commands\PlayCommand;
use DavidPeach\Manuscript\Commands\StatusCommand;
use DavidPeach\Manuscript\Container\Container;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestCase extends KernelTestCase
{
    protected Container $mContainer;

    protected array $commands = [
        'init' => InitCommand::class,
        'create' => CreateCommand::class,
        'clear' => ClearPlaygroundsCommand::class,
        'play' => PlayCommand::class,
        'status' => StatusCommand::class,
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->mContainer = new Container();
    }

    protected function getCommand(string $command)
    {
        return $this->mContainer->get($command);
    }
}

