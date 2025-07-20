<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\formatStylish;
use function Differ\Formatters\Plain\formatPlain;

function format(array $tree, string $formatName): string
{
    switch ($formatName) {
        case 'stylish':
            return formatStylish($tree);
        case 'plain':
            return formatPlain($tree);
        default:
            throw new \Exception("Unknown format: {$formatName}");
    }
}
