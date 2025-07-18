<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testFlatJsonDiff(): void
    {
        $file1 = __DIR__ . '/fixtures/file1.json';
        $file2 = __DIR__ . '/fixtures/file2.json';
        $expected = <<<EOD
{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}
EOD;

        $this->assertSame($expected, genDiff($file1, $file2));
    }
}
