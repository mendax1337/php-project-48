<?php

namespace Differ\Differ;

use function Differ\Parser\parseFile;
use function Differ\Formatters\render;

const ADDED = 'added';
const REMOVED = 'deleted';
const UNCHANGED = 'unchanged';
const CHANGED = 'changed';
const NESTED = 'nested';

function genDiff(string $filePath1, string $filePath2, string $format = 'stylish'): string
{
    $content1 = parseFile($filePath1);
    $content2 = parseFile($filePath2);

    $diff = createDiffTree($content1, $content2);

    return render($diff, $format);
}

/**
 * @param array<string, mixed> $data1
 * @param array<string, mixed> $data2
 * @return array<int, array<string, mixed>>
 */
function createDiffTree(array $data1, array $data2): array
{
    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    sort($allKeys);

    $tree = [];
    foreach ($allKeys as $key) {
        $has1 = array_key_exists($key, $data1);
        $has2 = array_key_exists($key, $data2);
        $val1 = $has1 ? $data1[$key] : null;
        $val2 = $has2 ? $data2[$key] : null;

        if ($has1 && !$has2) {
            $tree[] = [
                'compare' => REMOVED,
                'key' => $key,
                'value' => $val1
            ];
        } elseif (!$has1 && $has2) {
            $tree[] = [
                'compare' => ADDED,
                'key' => $key,
                'value' => $val2
            ];
        } elseif (is_array($val1) && is_array($val2)) {
            $tree[] = [
                'compare' => NESTED,
                'key' => $key,
                'value' => createDiffTree($val1, $val2)
            ];
        } elseif ($val1 !== $val2) {
            $tree[] = [
                'compare' => CHANGED,
                'key' => $key,
                'value1' => $val1,
                'value2' => $val2
            ];
        } else {
            $tree[] = [
                'compare' => UNCHANGED,
                'key' => $key,
                'value' => $val1
            ];
        }
    }
    return $tree;
}
