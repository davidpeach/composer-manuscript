<?php

namespace Davidpeach\Manuscript;

class AddsToJsonFile
{
    public static function add(string $pathToFile, array $toAdd)
    {
        $composerArray = json_decode(file_get_contents($pathToFile), true);

        $composerArray = array_merge($composerArray, $toAdd);

        // Require needs to be an object.
        $composerArray['require'] = new \StdClass;

        $updatedComposerJson = json_encode($composerArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);

        file_put_contents($pathToFile, $updatedComposerJson);
    }
}
