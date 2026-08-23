<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\BuildingParser;
use Tests\Unit\TestCase;

class BuildingParserTest extends TestCase
{
    private BuildingParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new BuildingParser;
    }

    /**
     * v0 (レガシー) は climates/enables を保存しないため、layouts の直後に
     * flags が続く (building_reader.cc)。修正前は存在しない8バイト分を
     * 余分にスキップしており、flags が誤ったオフセットから読まれていた。
     */
    public function test_version0_reads_flags_immediately_after_layouts(): void
    {
        $payload = pack('v', 0);      // old_btyp (v)
        $payload .= pack('v', 0);     // skip (menupos相当)
        $payload .= pack('V', 39);    // type
        $payload .= pack('V', 3);     // level
        $payload .= pack('V', 0);     // extra_data
        $payload .= pack('v', 2);     // size_x
        $payload .= pack('v', 3);     // size_y
        $payload .= pack('V', 1);     // layouts
        $payload .= pack('V', 0x2A);  // flags (直後に来るはず)

        $result = $this->parser->parse($this->makeNode($payload));

        $this->assertSame(0, $result['version']);
        $this->assertSame(39, $result['type']);
        $this->assertSame(3, $result['level']);
        $this->assertSame(2, $result['size_x']);
        $this->assertSame(3, $result['size_y']);
        $this->assertSame(1, $result['layouts']);
        $this->assertSame(0x2A, $result['flags']);
    }

    /**
     * v7 は allow_underground を保存する (capacity/maintenance/price は v8 以降)。
     * 修正前は v8 未満の分岐に allow_underground の読み取りが含まれておらず、
     * 常に欠落していた。
     */
    public function test_version7_reads_allow_underground(): void
    {
        $payload = pack('C', 0);      // btyp
        $payload .= pack('C', 34);    // type (stop)
        $payload .= pack('v', 4);     // level
        $payload .= pack('V', 1);     // extra_data (waytype)
        $payload .= pack('v', 1);     // size_x
        $payload .= pack('v', 1);     // size_y
        $payload .= pack('C', 1);     // layouts
        $payload .= pack('v', 0x7F);  // allowed_climates
        $payload .= pack('C', 1);     // enables
        $payload .= pack('C', 0);     // flags
        $payload .= pack('C', 100);   // distribution_weight
        $payload .= pack('v', 23880); // intro_date
        $payload .= pack('v', 24240); // retire_date
        $payload .= pack('v', 300);   // animation_time
        $payload .= pack('C', 2);     // allow_underground

        $result = $this->parser->parse($this->makeVersionedNode(7, $payload));

        $this->assertSame(7, $result['version']);
        $this->assertArrayHasKey('allow_underground', $result);
        $this->assertSame(2, $result['allow_underground']);
        $this->assertArrayNotHasKey('capacity', $result);
    }

    /**
     * v8 では capacity/maintenance/price が allow_underground の手前に挿入される
     * (回帰防止: v7修正時に読み取り順序を壊していないことを確認する)。
     */
    public function test_version8_reads_capacity_maintenance_price_before_allow_underground(): void
    {
        $payload = pack('C', 0);      // btyp
        $payload .= pack('C', 34);    // type (stop)
        $payload .= pack('v', 4);     // level
        $payload .= pack('V', 1);     // extra_data
        $payload .= pack('v', 1);     // size_x
        $payload .= pack('v', 1);     // size_y
        $payload .= pack('C', 1);     // layouts
        $payload .= pack('v', 0x7F);  // allowed_climates
        $payload .= pack('C', 1);     // enables
        $payload .= pack('C', 0);     // flags
        $payload .= pack('C', 100);   // distribution_weight
        $payload .= pack('v', 23880); // intro_date
        $payload .= pack('v', 24240); // retire_date
        $payload .= pack('v', 300);   // animation_time
        $payload .= pack('v', 64);    // capacity
        $payload .= pack('l', 500);   // maintenance (int32)
        $payload .= pack('l', 10000); // price (int32)
        $payload .= pack('C', 1);     // allow_underground

        $result = $this->parser->parse($this->makeVersionedNode(8, $payload));

        $this->assertSame(8, $result['version']);
        $this->assertSame(64, $result['capacity']);
        $this->assertSame(500, $result['maintenance']);
        $this->assertSame(10000, $result['price']);
        $this->assertSame(1, $result['allow_underground']);
    }

    /**
     * v12 (未対応バージョン) は例外を投げる。BuildingParser はもともと
     * バージョン上限チェックが存在せず、未知の将来バージョンをv11と
     * 同じレイアウトで無警告のまま誤読していた (回帰防止)。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported building version: 12 (max known: 11)');

        $this->parser->parse($this->makeVersionedNode(12, ''));
    }

    private function makeNode(string $data): Node
    {
        $size = strlen($data);
        $binary = 'BUIL'.pack('v', 0).pack('v', $size).$data;

        return Node::parse(new BinaryReader($binary));
    }

    private function makeVersionedNode(int $version, string $payload): Node
    {
        $data = pack('v', 0x8000 | $version).$payload;

        return $this->makeNode($data);
    }
}
