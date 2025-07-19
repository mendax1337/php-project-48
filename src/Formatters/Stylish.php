<?php

namespace Differ\Formatters\Stylish;

const INDENT_STEP = 4;

function formatStylish(array $tree, int $depth = 1): string
{
    $lines = [];

    foreach ($tree as $node) {
        $indent = str_repeat(' ', $depth * INDENT_STEP - 2);

        switch ($node['type']) {
            case 'added':
                $valStr = toString($node['value'], $depth + 1);
                $lines[] = renderLine($indent, '+', $node['key'], $valStr);
                break;

            case 'removed':
                $valStr = toString($node['value'], $depth + 1);
                $lines[] = renderLine($indent, '-', $node['key'], $valStr);
                break;

            case 'unchanged':
                $valStr = toString($node['value'], $depth + 1);
                $lines[] = renderLine($indent, ' ', $node['key'], $valStr);
                break;

            case 'changed':
                $oldValStr = toString($node['oldValue'], $depth + 1);
                $newValStr = toString($node['newValue'], $depth + 1);
                $lines[] = renderLine($indent, '-', $node['key'], $oldValStr);
                $lines[] = renderLine($indent, '+', $node['key'], $newValStr);
                break;

            case 'nested':
                $childLines = formatStylish($node['children'], $depth + 1);
                $lines[] = renderLine($indent, ' ', $node['key'], $childLines);
                break;

            default:
                throw new \Exception("Unknown node type: {$node['type']}");
        }
    }

    $bracketIndent = str_repeat(' ', ($depth - 1) * INDENT_STEP);
    return "{\n" . implode("\n", $lines) . "\n{$bracketIndent}}";
}

function renderLine(string $indent, string $symbol, string $key, string $value): string
{
    if (str_starts_with($value, "{\n")) {
        return "{$indent}{$symbol} {$key}: {$value}";
    }
    return $value === ''
        ? "{$indent}{$symbol} {$key}:"
        : "{$indent}{$symbol} {$key}: {$value}";
}

function toString($value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if ($value === null) {
        return 'null';
    }
    if (!is_array($value)) {
        return (string)$value;
    }
    $indent = str_repeat(' ', $depth * INDENT_STEP);
    $closingIndent = str_repeat(' ', ($depth - 1) * INDENT_STEP);
    $lines = [];
    foreach ($value as $key => $val) {
        $lines[] = "{$indent}{$key}: " . toString($val, $depth + 1);
    }
    return "{\n" . implode("\n", $lines) . "\n{$closingIndent}}";
}
