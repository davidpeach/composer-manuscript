<?php

namespace Davidpeach\Manuscript;

abstract class Package
{
    protected $data = [
        'name' => '',
        'description' => '',
        'author' => '',
        'stability' => '',
        'license' => '',
    ];

    protected $input;

    protected $output;

    protected $helper;

    protected $directory;

    protected $namespace;

    // new props
    protected $name;

    protected $path;

    public function __construct($input, $output, $helper, $directory)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helper = $helper;
        $this->directory = $directory;
    }

    abstract public function getPath(): string;

    abstract public function getName(): string;

    abstract public function getData(): void;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function setDependancies(array $dependancies): void
    {
        $this->dependancies = $dependancies;
    }
}
