<?php

namespace Differ\Formatters\Plain;

function formatPlain(array $tree, string $ancestry = ''): string
{
    $lines = [];

    foreach ($tree as $node) {
        $property = $ancestry . $node['key'];

        switch ($node['type']) {
            case 'nested':
                $lines[] = formatPlain($node['children'], $property . '.');
                break;

            case 'added':
                $lines[] = "Property '{$property}' was added with value: " . toPlainValue($node['value']);
                break;

            case 'removed':
                $lines[] = "Property '{$property}' was removed";
                break;

            case 'changed':
                $lines[] = "Property '{$property}' was updated. From " . toPlainValue($node['oldValue']) . " to " . toPlainValue($node['newValue']);
                break;
        }
    }

    return implode("\n", array_filter($lines));
}

function toPlainValue($value): string
{
    if (is_array($value)) {
        return '[complex value]';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if ($value === '') {
        return "''";
    }
    if (is_string($value)) {
        return "'" . $value . "'";
    }
    return (string)$value;
}
