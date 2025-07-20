<?php

namespace Differ\Formatters\Plain;

use const Differ\Differ\ADDED;
use const Differ\Differ\REMOVED;
use const Differ\Differ\CHANGED;
use const Differ\Differ\NESTED;

/**
 * @param array<int, array<string, mixed>> $tree
 * @param string $ancestry
 * @return string
 */
function displayPlain(array $tree, string $ancestry = ''): string
{
    $lines = [];
    foreach ($tree as $node) {
        $key = isset($node['key']) ? (is_scalar($node['key']) ? (string)$node['key'] : '') : '';
        $property = $ancestry === '' ? $key : "{$ancestry}.{$key}";
        switch ($node['compare']) {
            case ADDED:
                $lines[] = "Property '{$property}' was added with value: " . stringify($node['value'] ?? null);
                break;
            case REMOVED:
                $lines[] = "Property '{$property}' was removed";
                break;
            case CHANGED:
                $from = stringify($node['value1'] ?? null);
                $to = stringify($node['value2'] ?? null);
                $lines[] = "Property '{$property}' was updated. From {$from} to {$to}";
                break;
            case NESTED:
                if (isset($node['value']) && is_array($node['value'])) {
                    $lines[] = displayPlain($node['value'], $property);
                }
                break;
        }
    }
    return implode("\n", array_filter($lines));
}

/**
 * @param mixed $value
 * @return string
 */
function stringify(mixed $value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if ($value === null) {
        return 'null';
    }
    if (is_array($value)) {
        return '[complex value]';
    }
    if (is_string($value)) {
        return "'{$value}'";
    }
    if (is_scalar($value)) {
        return (string)$value;
    }
    return '';
}
