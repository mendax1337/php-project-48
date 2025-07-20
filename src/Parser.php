<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

/**
 * @param string $filepath
 * @return array<string, mixed>
 */
function parseFile(string $filepath): array
{
    $realPath = realpath($filepath);
    if ($realPath === false) {
        throw new \RuntimeException("File not found: {$filepath}");
    }

    $content = file_get_contents($realPath);
    if ($content === false) {
        throw new \RuntimeException("Can't read file: {$filepath}");
    }

    $ext = pathinfo($realPath, PATHINFO_EXTENSION);

    $data = match (strtolower($ext)) {
        'json' => json_decode($content, true, flags: JSON_THROW_ON_ERROR),
        'yml', 'yaml' => Yaml::parse($content),
        default => throw new \RuntimeException("Unsupported file extension: {$ext}"),
    };

    if (!is_array($data)) {
        throw new \RuntimeException("Parsed data is not an array in file: {$filepath}");
    }
    return $data;
}
