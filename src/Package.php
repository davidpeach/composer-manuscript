<?php

namespace DavidPeach\Manuscript;

abstract class Package
{
    public string $name;

    public string $description;

    public string $authorName;

    public string $authorEmail;

    public string $author;

    public string $stability;

    public string $license;

    protected string $path;

    public function __construct(
        protected string          $directory,
        protected QuestionAsker   $questions,
        protected ComposerFileManager $composerFileManager
    )
    {}

    public function getName(): string
    {
        return $this->name;
    }

    public function folderName(): string
    {
        $parts = explode('/', $this->name);
        return end($parts);
    }

    public function package(): self
    {
        return $this;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

//    abstract public function getPath(): string;

    abstract public function getData(): self;
}
