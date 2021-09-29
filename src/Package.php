<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Package
{
    protected $data = [
        'name' => '',
        'description' => '',
        'author' => '',
        'stability' => '',
        'license' => '',
    ];

    protected InputInterface $input;

    protected OutputInterface $output;

    protected Helper $helper;

    protected string $directory;

    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $helper, string
    $directory)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helper = $helper;
        $this->directory = $directory;
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    abstract public function getPath(): string;

    abstract public function getData(): void;
}
