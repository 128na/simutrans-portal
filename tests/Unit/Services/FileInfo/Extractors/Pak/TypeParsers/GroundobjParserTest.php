<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\GroundobjParser;
use Tests\Unit\TestCase;

class GroundobjParserTest extends TestCase
{
    private GroundobjParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new GroundobjParser;
    }

    /**
     * v0 は存在しないフォーマットとして例外を投げる (groundobj_reader.cc)。
     */
    public function test_version0_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported groundobj version: 0 (max known: 2)');

        $this->parser->parse($this->makeNode(pack('v', 100)));
    }

    /**
     * v3 (未対応バージョン) は例外を投げる (回帰防止)。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported groundobj version: 3 (max known: 2)');

        $this->parser->parse($this->makeNode(pack('v', 0x8000 | 3)));
    }

    private function makeNode(string $data): Node
    {
        $size = strlen($data);
        $binary = 'GOBJ'.pack('v', 0).pack('v', $size).$data;

        return Node::parse(new BinaryReader($binary));
    }
}
