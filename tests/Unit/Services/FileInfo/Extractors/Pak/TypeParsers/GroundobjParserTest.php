<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\GroundobjParser;
use Tests\Unit\Services\FileInfo\Extractors\Pak\MakesTestNodes;
use Tests\Unit\TestCase;

class GroundobjParserTest extends TestCase
{
    use MakesTestNodes;

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

        $this->parser->parse($this->makeNode('GOBJ', pack('v', 100)));
    }

    /**
     * v3 (未対応バージョン) は例外を投げる (回帰防止)。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported groundobj version: 3 (max known: 2)');

        $this->parser->parse($this->makeNode('GOBJ', pack('v', 0x8000 | 3)));
    }
}
