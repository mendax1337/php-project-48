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

    /** @dataProvider diffProvider */
    public function testGenDiff(string $file1, string $file2, string $format, string $expectedFile): void
    {
        $expected = file_get_contents("{$this->fixturesDir}/{$expectedFile}");
        $this->assertSame(
            $expected,
            genDiff("{$this->fixturesDir}/{$file1}", "{$this->fixturesDir}/{$file2}", $format)
        );
    }

    /**
     * @return array<int, array{string, string, string, string}>
     */
    public static function diffProvider(): array
    {
        return [
            // stylish
            ['file1.json', 'file2.json', 'stylish', 'stylish-expected.txt'],
            ['file1.yml', 'file2.yml', 'stylish', 'stylish-expected.txt'],
            ['file1.json', 'file2.yml', 'stylish', 'stylish-expected.txt'],
            ['file1.yml', 'file2.json', 'stylish', 'stylish-expected.txt'],

            // plain
            ['file1.json', 'file2.json', 'plain', 'plain-expected.txt'],
            ['file1.yml', 'file2.yml', 'plain', 'plain-expected.txt'],
            ['file1.json', 'file2.yml', 'plain', 'plain-expected.txt'],
            ['file1.yml', 'file2.json', 'plain', 'plain-expected.txt'],

            // json
            ['file1.json', 'file2.json', 'json', 'json-expected.txt'],
            ['file1.yml', 'file2.json', 'json', 'json-expected.txt'],
            ['file1.yml', 'file2.yml', 'json', 'json-expected.txt'],
            ['file1.json', 'file2.yml', 'json', 'json-expected.txt'],
        ];
    }
}
