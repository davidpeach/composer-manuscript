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
    }

    /**
     * @return array | null
     */
    public function getConfigData(): array|null
    {
        return json_decode(
            json: file_get_contents(filename: $this->getFullConfigPath()),
            associative: true
        );
    }

    /**
     * @return string
     */
    public function gitPersonalAccessToken(): string
    {
        return $this->getConfigData()['git_personal_access_token'] ?? false;

    }

    /**
     * @param $key
     * @param string|array $value
     */
    public function update($key, string|array $value): void
    {
         $configFile = $this->getFullConfigPath();
         $configData = $this->getConfigData();

         $configData[$key] = $value;
         file_put_contents(filename: $configFile, data: json_encode(value: $configData));
    }

    public function exists(): bool
    {
        return $this->filesystem->exists(files:  $this->getFullConfigPath());
    }

    public function init(): void
    {
        $this->filesystem->touch(files: $this->getFullConfigPath());
        $this->update(key: 'created_at', value: time());
    }

    /**
     * @return string
     */
    private function getFullConfigPath(): string
    {
        return $this->directory . '/' . self::MANUSCRIPT_CONFIG;
    }
}
