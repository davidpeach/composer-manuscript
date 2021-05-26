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

    protected $installDirectory;

    public function __construct($input, $output, $helper, $installDirectory)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helper = $helper;
        $this->installDirectory = $installDirectory;
    }

    abstract public function getDirectory();

    abstract public function getName();

    abstract public function getData();
}
