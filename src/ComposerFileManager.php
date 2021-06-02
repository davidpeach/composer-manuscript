<?php

namespace Davidpeach\Manuscript;

use Illuminate\Support\Str;

class ComposerFileManager
{
    const COMPOSER_FILE_NAME = 'composer.json';

    public static function read(string $pathToFile): array
    {
        if (! Str::endsWith($pathToFile, self::COMPOSER_FILE_NAME)) {
            Str::finish($pathToFile, '/') . self::COMPOSER_FILE_NAME;
        }

        return json_decode(file_get_contents($pathToFile), true);
    }

    public static function add(string $pathToFile, array $toAdd): void
    {
        $composerArray = array_merge_recursive(
            self::read($pathToFile),
            $toAdd
        );

        // Require needs to be an object.
        $composerArray['require'] = (object) $composerArray['require'];

        $updatedComposerJson = json_encode($composerArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);

        file_put_contents($pathToFile, $updatedComposerJson);
    }
}
