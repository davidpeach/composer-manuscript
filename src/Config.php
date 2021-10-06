<?php

namespace DavidPeach\Manuscript;

use Symfony\Component\Filesystem\Filesystem;

class Config
{
    const MANUSCRIPT_CONFIG = '.manuscript';

    public function __construct(
        private string $directory,
        private Filesystem $filesystem,
    )
    {
        $this->ensureConfigExists();
    }

    public function getConfigData()
    {
        return json_decode(
            file_get_contents($this->getFullConfigPath()),
            true
        );
    }

    public function gitPersonalAccessToken(): string
    {
        return $this->getConfigData()['git_personal_access_token'] ?? false;

    }

    public function updateConfig($key, string|array $value)
    {
         $configFile = $this->getFullConfigPath();
         $configData = $this->getConfigData();

         $configData[$key] = $value;
         file_put_contents($configFile, json_encode($configData));
    }

    private function ensureConfigExists()
    {
        $configFile = $this->getFullConfigPath();

        if (!$this->filesystem->exists($configFile)) {
            $this->filesystem->touch($configFile);
            $this->updateConfig('init', []);
        }
    }

    private function getFullConfigPath(): string
    {
        return $this->directory . '/' . self::MANUSCRIPT_CONFIG;
    }
}
