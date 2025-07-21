<?php

namespace Differ\Formatters\Stylish;

use const Differ\Differ\ADDED;
use const Differ\Differ\REMOVED;
use const Differ\Differ\UNCHANGED;
use const Differ\Differ\CHANGED;
use const Differ\Differ\NESTED;

const INDENT = ' ';
const BASE_INDENT = 4;
const SIGN_LENGTH = 2;

const SIGN_MAP = [
    ADDED => '+',
    REMOVED => '-',
    UNCHANGED => ' ',
    CHANGED => ' ',
    NESTED => ' ',
];

/**
 * @param array<int, array<string, mixed>> $nodes
 * @param int $depth
 * @return string
 */
function displayStylish(array $nodes, int $depth = 1): string
{
    $indent = str_repeat(INDENT, $depth * BASE_INDENT - SIGN_LENGTH);
    $closeIndent = str_repeat(INDENT, ($depth - 1) * BASE_INDENT);

    $lines = array_map(
        static function (array $node) use ($indent, $depth): string {
            $key = $node['key'];
            $type = $node['compare'];
            return match ($type) {
                CHANGED => sprintf(
                    "%s- %s: %s\n%s+ %s: %s",
                    $indent,
                    $key,
                    formatValue($node['value1'], $depth + 1),
                    $indent,
                    $key,
                    formatValue($node['value2'], $depth + 1)
                ),
                ADDED, REMOVED, UNCHANGED => sprintf(
                    "%s%s %s: %s",
                    $indent,
                    SIGN_MAP[$type],
                    $key,
                    formatValue($node['value'], $depth + 1)
                ),
                NESTED => sprintf(
                    "%s  %s: %s",
                    $indent,
                    $key,
                    displayStylish($node['value'], $depth + 1)
                ),
                default => '',
            };
        },
        $nodes
    );
    return "{\n" . implode("\n", $lines) . "\n{$closeIndent}}";
}

/**
 * @param mixed $val
 * @param int $depth
 * @return string
 */
function formatValue($val, int $depth): string
{
    if (is_bool($val)) {
        return $val ? 'true' : 'false';
    }
    if ($val === null) {
        return 'null';
    }
    if (!is_array($val)) {
        return (string)$val;
    }
    $indent = str_repeat(INDENT, $depth * BASE_INDENT);
    $closeIndent = str_repeat(INDENT, ($depth - 1) * BASE_INDENT);
    $result = array_map(
        static function ($k, $v) use ($depth, $indent) {
            return "{$indent}{$k}: " . formatValue($v, $depth + 1);
        },
        array_keys($val),
        $val
    );
    return "{\n" . implode("\n", $result) . "\n{$closeIndent}}";
}
