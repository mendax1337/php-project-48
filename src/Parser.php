<?php

namespace Gendiff;

function parseFile(string $filepath): array
{
    $content = file_get_contents($filepath);
    if ($content === false) {
        throw new \Exception("Can't read file: {$filepath}");
    }

    $data = json_decode($content, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception("Invalid JSON in file: {$filepath}");
    }

    return $data;
}
