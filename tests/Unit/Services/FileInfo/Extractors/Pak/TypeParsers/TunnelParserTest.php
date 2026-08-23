<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\TunnelParser;
use Tests\Unit\Services\FileInfo\Extractors\Pak\MakesTestNodes;
use Tests\Unit\TestCase;

class TunnelParserTest extends TestCase
{
    use MakesTestNodes;

    private TunnelParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new TunnelParser;
    }

    /**
     * v0 (レガシー、バージョンスタンプなし) は未対応として例外を投げる。
     */
    public function test_version0_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported tunnel version: 0 (max known: 6)');

        $this->parser->parse($this->makeNode('TUNL', pack('v', 100)));
    }

    /**
     * v7 (未対応バージョン) は例外を投げる (回帰防止)。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported tunnel version: 7 (max known: 6)');

        $this->parser->parse($this->makeNode('TUNL', pack('v', 0x8000 | 7)));
    }
}
