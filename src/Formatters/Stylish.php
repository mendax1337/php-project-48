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
    $lines = [];

    foreach ($nodes as $node) {
        $key = isset($node['key']) ? (is_scalar($node['key']) ? (string)$node['key'] : '') : '';
        $type = $node['compare'] ?? '';
        switch ($type) {
            case CHANGED:
                $lines[] = sprintf(
                    "%s- %s: %s",
                    $indent,
                    $key,
                    formatValue($node['value1'] ?? null, $depth + 1)
                );
                $lines[] = sprintf(
                    "%s+ %s: %s",
                    $indent,
                    $key,
                    formatValue($node['value2'] ?? null, $depth + 1)
                );
                break;
            case ADDED:
            case REMOVED:
            case UNCHANGED:
                $sign = SIGN_MAP[$type] ?? ' ';
                $lines[] = sprintf(
                    "%s%s %s: %s",
                    $indent,
                    $sign,
                    $key,
                    formatValue($node['value'] ?? null, $depth + 1)
                );
                break;
            case NESTED:
                if (isset($node['value']) && is_array($node['value'])) {
                    $lines[] = sprintf(
                        "%s  %s: %s",
                        $indent,
                        $key,
                        displayStylish($node['value'], $depth + 1)
                    );
                }
                break;
        }
    }
    return "{\n" . implode("\n", $lines) . "\n{$closeIndent}}";
}

/**
 * @param mixed $val
 * @param int $depth
 * @return string
 */
function formatValue(mixed $val, int $depth): string
{
    if (is_bool($val)) {
        return $val ? 'true' : 'false';
    }
    if ($val === null) {
        return 'null';
    }
    if (!is_array($val)) {
        if (is_scalar($val)) {
            return (string)$val;
        }
        return '';
    }
    $indent = str_repeat(INDENT, $depth * BASE_INDENT);
    $closeIndent = str_repeat(INDENT, ($depth - 1) * BASE_INDENT);
    $result = [];
    foreach ($val as $k => $v) {
        $key = (string)$k;
        $result[] = "{$indent}{$key}: " . formatValue($v, $depth + 1);
    }
    return "{\n" . implode("\n", $result) . "\n{$closeIndent}}";
}
