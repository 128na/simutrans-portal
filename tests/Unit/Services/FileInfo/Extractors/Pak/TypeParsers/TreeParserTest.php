<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\TreeParser;
use Tests\Unit\Services\FileInfo\Extractors\Pak\MakesTestNodes;
use Tests\Unit\TestCase;

class TreeParserTest extends TestCase
{
    use MakesTestNodes;

    private TreeParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new TreeParser;
    }

    /**
     * v3 (未対応バージョン) は例外を投げる (回帰防止)。v0 はレガシー形式として
     * サポートされているため、v0 は対象外。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported tree version: 3 (max known: 2)');

        $this->parser->parse($this->makeNode('TREE', pack('v', 0x8000 | 3)));
    }
}
