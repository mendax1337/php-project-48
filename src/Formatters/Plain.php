<?php

namespace Differ\Formatters\Plain;

use const Differ\Differ\ADDED;
use const Differ\Differ\REMOVED;
use const Differ\Differ\CHANGED;
use const Differ\Differ\NESTED;

function displayPlain(array $tree, string $ancestry = ''): string
{
    $lines = [];
    foreach ($tree as $node) {
        $property = $ancestry === '' ? $node['key'] : "{$ancestry}.{$node['key']}";
        switch ($node['compare']) {
            case ADDED:
                $lines[] = "Property '{$property}' was added with value: " . stringify($node['value']);
                break;
            case REMOVED:
                $lines[] = "Property '{$property}' was removed";
                break;
            case CHANGED:
                $from = stringify($node['value1']);
                $to = stringify($node['value2']);
                $lines[] = "Property '{$property}' was updated. From {$from} to {$to}";
                break;
            case NESTED:
                $lines[] = displayPlain($node['value'], $property);
                break;
        }
    }
    return implode("\n", array_filter($lines));
}

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
