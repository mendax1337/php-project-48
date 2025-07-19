<?php

declare(strict_types=1);

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private string $fixturesDir;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/fixtures';
    }

    public function testStylishJson(): void
    {
        $file1 = "{$this->fixturesDir}/file1.json";
        $file2 = "{$this->fixturesDir}/file2.json";
        $expected = file_get_contents("{$this->fixturesDir}/stylish-expected.txt");
        $this->assertSame($expected, genDiff($file1, $file2));
    }

    public function testStylishYaml(): void
    {
        $file1 = "{$this->fixturesDir}/file1.yml";
        $file2 = "{$this->fixturesDir}/file2.yml";
        $expected = file_get_contents("{$this->fixturesDir}/stylish-expected.txt");
        $this->assertSame($expected, genDiff($file1, $file2));
    }
}
