<?php

namespace DavidPeach\Manuscript\Playground;

use DavidPeach\Manuscript\Frameworks\Framework;
use DavidPeach\Manuscript\Package;

class Playground
{
    private string $folderFormat = '%s-%s';

    private string $path;

    private string $name;

    private string $folderName;

    protected string $baseDirectory;

    protected Framework $framework;

    protected Package $package;

    public function setBaseDirectory(string $directory): void
    {
        $this->baseDirectory = $directory;
    }

    public function setFramework(Framework $framework): void
    {
        $this->framework = $framework;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setFolderName(string $folderName):void
    {
        $this->folderName = $folderName;
    }

    public function getFolderName(): string
    {
        return $this->folderName;
    }

    public function setPackage(Package $package)
    {
        $this->package = $package;
    }

    public function getFolderFormat(): string
    {
        return $this->folderFormat;
    }
}
