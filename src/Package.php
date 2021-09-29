<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Package
{
    public string $name;

    public string $description;

    public string $authorName;

    public string $authorEmail;

    public string $author;

    public string $stability;

    public string $license;

    public function __construct(
        protected InputInterface  $input,
        protected OutputInterface $output,
        protected QuestionHelper  $helper,
        protected string          $directory
    )
    {
        $this->getData();
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function getPath(): string;

    abstract public function getData(): void;
}
