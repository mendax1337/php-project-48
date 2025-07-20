<?php

namespace Differ\Formatters\Json;

use JsonException;

function formatJson(array $data): string
{
    return json_encode($data, JSON_THROW_ON_ERROR) . PHP_EOL;
}
