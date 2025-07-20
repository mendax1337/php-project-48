<?php

namespace Differ\Formatters\Plain;

function formatPlain(array $tree): string
{
    $lines = iter($tree);
    return implode("\n", array_filter($lines));
}

function iter(array $tree, string $ancestry = ''): array
{
    $lines = [];
    foreach ($tree as $node) {
        $property = $ancestry === '' ? $node['key'] : "{$ancestry}.{$node['key']}";
        switch ($node['type']) {
            case 'nested':
                $lines = array_merge($lines, iter($node['children'], $property));
                break;
            case 'added':
                $lines[] = "Property '{$property}' was added with value: " . formatValue($node['value']);
                break;
            case 'removed':
                $lines[] = "Property '{$property}' was removed";
                break;
            case 'changed':
                $old = formatValue($node['oldValue']);
                $new = formatValue($node['newValue']);
                $lines[] = "Property '{$property}' was updated. From {$old} to {$new}";
                break;
        }
    }
    return $lines;
}

function formatValue($value): string
{
    if (is_array($value)) {
        return '[complex value]';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if ($value === null) {
        return 'null';
    }
    if (is_string($value)) {
        return "'" . $value . "'";
    }
    return (string)$value;
}
