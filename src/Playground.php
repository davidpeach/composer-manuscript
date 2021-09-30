<?php

namespace DavidPeach\Manuscript;

use Carbon\Carbon;
use DavidPeach\Manuscript\Frameworks\Framework;

class Playground
{
    private $folderFormat = '%s-%s';

    private $path;

    protected $baseDirectory;

    protected $framework;

    protected $folderOverride = null;

    public function setFolderOverride(string $folder)
    {
        $this->folderOverride = trim($folder, '/');
    }

    public function setBaseDirectory(string $directory): void
    {
        $this->baseDirectory = $directory;
    }

    public function setFramework(Framework $framework): void
    {
        $this->framework = $framework;
    }

    public function determinePath(): void
    {
        if ($this->folderOverride) {
            $this->setPath($this->baseDirectory . $this->folderOverride);
            return;
        }

        $folder = vsprintf($this->folderFormat, [
            $this->framework->folderFormat(),
            Carbon::now()->timestamp,
        ]);

        $this->setPath($this->baseDirectory . $folder);
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFolderName(): string
    {
        $fullPathParts = explode('/', $this->getPath());
        return end($fullPathParts);
    }
}
