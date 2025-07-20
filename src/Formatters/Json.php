<?php

namespace Differ\Formatters\Json;

function displayJson(array $tree): string
{
    return json_encode($tree, JSON_THROW_ON_ERROR) . PHP_EOL;
}
