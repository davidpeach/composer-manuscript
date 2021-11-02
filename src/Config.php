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
            json: file_get_contents(filename: $this->getFullConfigPath()),
            associative: true
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
         file_put_contents(filename: $configFile, data: json_encode(value: $configData));
    }

    private function ensureConfigExists()
    {
        $configFile = $this->getFullConfigPath();

        if (!$this->filesystem->exists(files: $configFile)) {
            $this->filesystem->touch(files: $configFile);
            $this->updateConfig(key: 'init', value: []);
        }
    }

    private function getFullConfigPath(): string
    {
        return $this->directory . '/' . self::MANUSCRIPT_CONFIG;
    }
}
