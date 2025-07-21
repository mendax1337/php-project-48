<?php

namespace Differ\Differ;

use function Differ\Parser\parseFile;
use function Differ\Formatters\render;

const ADDED = 'added';
const REMOVED = 'deleted';
const UNCHANGED = 'unchanged';
const CHANGED = 'changed';
const NESTED = 'nested';

/**
 * @param string $filePath1
 * @param string $filePath2
 * @param string $format
 * @return string
 */
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
 * @return list<array<string, mixed>>
 */
function createDiffTree(array $data1, array $data2): array
{
    /** @var list<string> $allKeys */
    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $insertSorted = function (array $keys): array {
        return array_reduce(
            $keys,
            function ($sorted, $key) {
                $pos = 0;
                while ($pos < count($sorted) && $sorted[$pos] < $key) {
                    $pos++;
                }
                array_splice($sorted, $pos, 0, [$key]);
                return $sorted;
            },
            []
        );
    };

    $sortedKeys = $insertSorted($allKeys);

    return array_map(
        static function ($key) use ($data1, $data2) {
            $has1 = array_key_exists($key, $data1);
            $has2 = array_key_exists($key, $data2);
            $val1 = $has1 ? $data1[$key] : null;
            $val2 = $has2 ? $data2[$key] : null;

            if ($has1 && !$has2) {
                return [
                    'compare' => REMOVED,
                    'key' => $key,
                    'value' => $val1,
                ];
            }
            if (!$has1 && $has2) {
                return [
                    'compare' => ADDED,
                    'key' => $key,
                    'value' => $val2,
                ];
            }
            if (is_array($val1) && is_array($val2)) {
                return [
                    'compare' => NESTED,
                    'key' => $key,
                    'value' => createDiffTree($val1, $val2),
                ];
            }
            if ($val1 !== $val2) {
                return [
                    'compare' => CHANGED,
                    'key' => $key,
                    'value1' => $val1,
                    'value2' => $val2,
                ];
            }
            return [
                'compare' => UNCHANGED,
                'key' => $key,
                'value' => $val1,
            ];
        },
        $sortedKeys
    );
}
