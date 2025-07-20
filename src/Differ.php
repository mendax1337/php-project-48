<?php

namespace Differ\Differ;

use function Differ\Parser\parseFile;
use function Differ\Formatters\format;

const ADDED = 'added';
const REMOVED = 'removed';
const UNCHANGED = 'unchanged';
const CHANGED = 'changed';
const NESTED = 'nested';

function genDiff(string $filePath1, string $filePath2, string $format = 'stylish'): string
{
    $data1 = parseFile($filePath1);
    $data2 = parseFile($filePath2);

    $diffTree = buildDiffTree($data1, $data2);

    return format($diffTree, $format);
}

function buildDiffTree(array $data1, array $data2): array
{
    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    sort($allKeys);

    $diff = [];
    foreach ($allKeys as $key) {
        $exists1 = array_key_exists($key, $data1);
        $exists2 = array_key_exists($key, $data2);
        $value1 = $exists1 ? $data1[$key] : null;
        $value2 = $exists2 ? $data2[$key] : null;

        if ($exists1 && !$exists2) {
            $diff[] = ['key' => $key, 'type' => REMOVED, 'value' => $value1];
        } elseif (!$exists1 && $exists2) {
            $diff[] = ['key' => $key, 'type' => ADDED, 'value' => $value2];
        } elseif (is_array($value1) && is_array($value2)) {
            $diff[] = ['key' => $key, 'type' => NESTED, 'children' => buildDiffTree($value1, $value2)];
        } elseif ($value1 !== $value2) {
            $diff[] = ['key' => $key, 'type' => CHANGED, 'oldValue' => $value1, 'newValue' => $value2];
        } else {
            $diff[] = ['key' => $key, 'type' => UNCHANGED, 'value' => $value1];
        }
    }
    return $diff;
}
