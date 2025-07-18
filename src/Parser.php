<?php
namespace Differ\Parser;

class ParseError extends \Exception {}

function parseFile(string $filepath): array
{
    $realPath = realpath($filepath);
    if ($realPath === false) {
        throw new ParseError("File not found: {$filepath}");
    }

    $content = file_get_contents($realPath);
    if ($content === false) {
        throw new ParseError("Can't read file: {$filepath}");
    }

    $data = json_decode($content, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        throw new ParseError("Invalid JSON in file: {$filepath}");
    }

    return $data;
}
