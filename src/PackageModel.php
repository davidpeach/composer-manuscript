<?php

namespace DavidPeach\Manuscript;

class PackageModel
{
    private string $name;

    private string $path;

    private string $folderName;

    public function __construct()
    {
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

    public function setFolderName(string $folderName): void
    {
        $this->folderName = $folderName;
    }

    public function getFolderName(): string
    {
        return $this->folderName;
    }
}
