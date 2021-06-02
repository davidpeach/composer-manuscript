<?php

namespace Davidpeach\Manuscript;

use Davidpeach\Manuscript\Frameworks\Framework;

class Playground
{
    private $folderFormat = 'manuscript-playground-%s-%s';

    private $path;

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
        $folder = vsprintf($this->folderFormat, [
            $this->framework->folderFormat(),
            time(),
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
}
