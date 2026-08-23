<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\PedestrianParser;
use Tests\Unit\TestCase;

class PedestrianParserTest extends TestCase
{
    private PedestrianParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new PedestrianParser;
    }

    /**
     * v3 (未対応バージョン) は例外を投げる (回帰防止)。v0 はレガシー形式として
     * サポートされているため、v0 は対象外。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported pedestrian version: 3 (max known: 2)');

        $this->parser->parse($this->makeNode(pack('v', 0x8000 | 3)));
    }

    private function makeNode(string $data): Node
    {
        $size = strlen($data);
        $binary = 'PASS'.pack('v', 0).pack('v', $size).$data;

        return Node::parse(new BinaryReader($binary));
    }
}
