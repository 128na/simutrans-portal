<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\WayObjectParser;
use Tests\Unit\TestCase;

class WayObjectParserTest extends TestCase
{
    private WayObjectParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new WayObjectParser;
    }

    /**
     * WayObjectParser は高位ビットの有無に関わらず下位15bitをそのままバージョンとして
     * 扱う (way_obj_reader.cc相当の実装)。raw=0 は version=0 となり未対応として
     * 例外を投げる。
     */
    public function test_version0_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported wayobj version: 0 (max known: 2)');

        $this->parser->parse($this->makeNode(pack('v', 0)));
    }

    /**
     * v3 (未対応バージョン) は例外を投げる (回帰防止)。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported wayobj version: 3 (max known: 2)');

        $this->parser->parse($this->makeNode(pack('v', 0x8000 | 3)));
    }

    private function makeNode(string $data): Node
    {
        $size = strlen($data);
        $binary = 'WYOB'.pack('v', 0).pack('v', $size).$data;

        return Node::parse(new BinaryReader($binary));
    }
}
