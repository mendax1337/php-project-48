<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\formatStylish;
use function Differ\Formatters\Plain\formatPlain;
use function Differ\Formatters\Json\formatJson;

function format(array $diffTree, string $format): string
{
    return match ($format) {
        'stylish' => formatStylish($diffTree),
        'plain' => formatPlain($diffTree),
        'json' => formatJson($diffTree),
        default => throw new \Exception("Unknown format: {$format}")
    };
}
