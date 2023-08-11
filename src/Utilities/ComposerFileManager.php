<?php

namespace DavidPeach\Manuscript\Utilities;

use DavidPeach\Manuscript\Exceptions\ComposerFileNotFoundException;

class ComposerFileManager
{
    const COMPOSER_FILE_NAME = 'composer.json';

    /**
     * @param string $pathToFile
     * @return array
     * @throws ComposerFileNotFoundException
     */
    public function read(string $pathToFile): array
    {
        if (!str_ends_with(haystack: $pathToFile, needle: self::COMPOSER_FILE_NAME)) {
            $pathToFile = rtrim(string: $pathToFile, characters: '/') . '/' . self::COMPOSER_FILE_NAME;
        }

        if (!file_exists(filename: $pathToFile)) {
            throw new ComposerFileNotFoundException(message: 'Composer file not found at ' . $pathToFile);
        }

        return json_decode(json: file_get_contents(filename: $pathToFile), associative: true);
    }

    /**
     * @param string $pathToFile
     * @param array $toAdd
     * @throws ComposerFileNotFoundException
     */
    public function add(string $pathToFile, array $toAdd): void
    {
        $composerArray = $this->merge($this->read(pathToFile: $pathToFile), $toAdd);

        $composerArray['require'] = (object)$composerArray['require'];

        $updatedComposerJson = json_encode(
            value: $composerArray,
            flags: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT
        );

        file_put_contents(filename: $pathToFile, data: $updatedComposerJson);
    }

    private function merge($composer, $wantToAdd): array
    {
        foreach ($wantToAdd as $key => $additions) {

            $adding = null;

            if (
                !array_key_exists(key: $key, array: $composer) ||
                !is_array($additions)
            ) {
                $composer = array_merge($composer, [
                    $key => $additions,
                ]);
                continue;
            }

            $existing = array_map(function ($item) use ($key) {
                return match ($key) {
                    'repositories' => $item['url'],
                };
            }, $composer[$key]);


            $adding = array_filter($additions, function ($addition) use ($existing, $key) {
                return match ($key) {
                    'repositories' => !in_array(needle: $addition['url'], haystack: $existing),
                };
            });

            if ($adding) {
                $composer = array_merge_recursive(
                    $composer,
                    [$key => $adding],     
                );
            }
        }

        return $composer;
    }
}
