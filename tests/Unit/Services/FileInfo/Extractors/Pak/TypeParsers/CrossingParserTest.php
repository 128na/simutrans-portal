<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\CrossingParser;
use Tests\Unit\Services\FileInfo\Extractors\Pak\MakesTestNodes;
use Tests\Unit\TestCase;

class CrossingParserTest extends TestCase
{
    use MakesTestNodes;

    private CrossingParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new CrossingParser;
    }

    /**
     * v0 (レガシー、バージョンスタンプなし) は未対応として例外を投げる。
     */
    public function test_version0_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported crossing version: 0 (max known: 2)');

        $this->parser->parse($this->makeNode('CRSS', pack('v', 100)));
    }

    /**
     * v3 (未対応バージョン) は例外を投げる (回帰防止)。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported crossing version: 3 (max known: 2)');

        $this->parser->parse($this->makeNode('CRSS', pack('v', 0x8000 | 3)));
    }
}
