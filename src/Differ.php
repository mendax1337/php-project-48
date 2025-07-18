<?php

namespace Differ\Differ;

use function Differ\Parser\parseFile;

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    $data1 = parseFile($pathToFile1);
    $data2 = parseFile($pathToFile2);

    $mergedKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = $mergedKeys;
    sort($sortedKeys);

    $diffLines = array_map(function ($key) use ($data1, $data2) {
        $has1 = array_key_exists($key, $data1);
        $has2 = array_key_exists($key, $data2);
        $val1 = $has1 ? formatValue($data1[$key]) : null;
        $val2 = $has2 ? formatValue($data2[$key]) : null;

        $result = '';
        if ($has1 && !$has2) {
            $result = "  - {$key}: {$val1}";
        } elseif (!$has1 && $has2) {
            $result = "  + {$key}: {$val2}";
        } elseif ($val1 !== $val2) {
            $result = "  - {$key}: {$val1}\n  + {$key}: {$val2}";
        } else {
            $result = "    {$key}: {$val1}";
        }

        return $result;
    }, $sortedKeys);
    return "{\n" . implode("\n", $diffLines) . "\n}";
}

function formatValue(mixed $value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if ($value === null) {
        return 'null';
    }

    return (string) $value;
}
