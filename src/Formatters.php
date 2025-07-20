<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\displayStylish;
use function Differ\Formatters\Plain\displayPlain;
use function Differ\Formatters\Json\displayJson;

/**
 * @param array<int, array<string, mixed>> $tree
 * @param string $format
 * @return string
 */
function render(array $tree, string $format): string
{
    return match ($format) {
        'stylish' => displayStylish($tree),
        'plain'   => displayPlain($tree),
        'json'    => displayJson($tree),
        default   => throw new \Exception("Unknown format: {$format}")
    };
}
