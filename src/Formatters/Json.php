<?php

namespace Differ\Formatters\Json;

/**
 * @param array<int, array<string, mixed>> $tree
 * @return string
 */
function displayJson(array $tree): string
{
    return json_encode($tree, JSON_THROW_ON_ERROR) . PHP_EOL;
}
