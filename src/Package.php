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
}
