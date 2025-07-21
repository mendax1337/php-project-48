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
    $lines = array_map(
        static function (array $node) use ($ancestry): ?string {
            $property = $ancestry === '' ? (string)$node['key'] : "{$ancestry}.{$node['key']}";
            return match ($node['compare']) {
                ADDED =>
                    "Property '{$property}' was added with value: " . stringify($node['value']),
                REMOVED =>
                "Property '{$property}' was removed",
                CHANGED => sprintf(
                    "Property '{$property}' was updated. From %s to %s",
                    stringify($node['value1']),
                    stringify($node['value2'])
                ),
                NESTED =>
                displayPlain($node['value'], $property),
                default => null,
            };
        },
        $tree
    );
    return implode("\n", array_filter($lines, static fn($v): bool => $v !== null));
}

/**
 * @param mixed $value
 * @return string
 */
function stringify($value): string
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
    return (string)$value;
}
