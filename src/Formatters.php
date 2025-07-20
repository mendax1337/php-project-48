<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\formatStylish;
use function Differ\Formatters\Plain\formatPlain;
use function Differ\Formatters\Json\formatJson;

function format(array $diff, string $format): string
{
    switch ($format) {
        case 'stylish':
            return formatStylish($diff);
        case 'plain':
            return formatPlain($diff);
        case 'json':
            return formatJson($diff);
        default:
            throw new \Exception("Unknown format: $format");
    }
}
