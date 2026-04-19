<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\WayParser;
use Tests\Unit\TestCase;

class WayParserTest extends TestCase
{
    private WayParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new WayParser;
    }

    /**
     * v1 のフィールド読み込み順序バグの修正を検証する。
     *
     * バグ: intro_date を先頭(1フィールド目)に読んでいた。
     * 正: price→maintenance→topspeed→max_weight→intro_date の順 (way_reader.cc:130-138)
     */
    public function test_version1_reads_fields_in_correct_order(): void
    {
        $data = pack('v', 1);         // version = 1
        $data .= pack('V', 10000);   // price
        $data .= pack('V', 2000);    // maintenance
        $data .= pack('V', 100);     // topspeed
        $data .= pack('V', 500);     // max_weight
        $data .= pack('V', 31840);   // intro_date_raw (1990*16) → converted: (31840/16)*12 + (31840%16) = 23880
        $data .= pack('C', 1);       // waytype (road)
        $data .= pack('C', 0);       // styp (flat)

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(10000, $result['price']);
        $this->assertSame(2000, $result['maintenance']);
        $this->assertSame(100, $result['topspeed']);
        $this->assertSame(500, $result['max_weight']);
        $this->assertSame(23880, $result['intro_date']);
        $this->assertSame(1, $result['waytype']);
    }

    /**
     * v8 で clip_below フィールドが読み取れることを検証する。
     */
    public function test_version8_parses_clip_below_from_binary(): void
    {
        $data = pack('v', 8);         // version = 8
        $data .= pack('V', 100000);  // price_lo
        $data .= pack('V', 0);       // price_hi
        $data .= pack('V', 1000);    // maintenance_lo
        $data .= pack('V', 0);       // maintenance_hi
        $data .= pack('V', 120);     // topspeed
        $data .= pack('V', 500);     // max_weight
        $data .= pack('v', 23880);   // intro_date
        $data .= pack('v', 24240);   // retire_date
        $data .= pack('v', 15);      // axle_load
        $data .= pack('C', 1);       // waytype (road)
        $data .= pack('C', 0);       // styp (flat)
        $data .= pack('C', 0);       // draw_as_obj
        $data .= pack('C', 1);       // clip_below = true
        $data .= pack('c', 0);       // number_of_seasons

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertArrayHasKey('clip_below', $result);
        $this->assertSame(1, $result['clip_below']);
        $this->assertSame(100000, $result['price']);
        $this->assertSame(15, $result['axle_load']);
    }

    /**
     * v8 未満では clip_below が waytype に基づいてデフォルト設定されることを検証する。
     * 非 powerline (road) → clip_below = true
     */
    public function test_version7_clip_below_defaults_to_true_for_road(): void
    {
        $data = pack('v', 7);         // version = 7
        $data .= pack('V', 0).pack('V', 0);  // price sint64
        $data .= pack('V', 0).pack('V', 0);  // maintenance sint64
        $data .= pack('V', 100);     // topspeed
        $data .= pack('V', 500);     // max_weight
        $data .= pack('v', 23880);   // intro_date
        $data .= pack('v', 24240);   // retire_date
        $data .= pack('v', 9999);    // axle_load
        $data .= pack('C', 1);       // waytype (road = 1, not powerline)
        $data .= pack('C', 0);       // styp
        $data .= pack('C', 0);       // draw_as_obj
        $data .= pack('c', 0);       // number_of_seasons

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertArrayHasKey('clip_below', $result);
        $this->assertTrue((bool) $result['clip_below']);
    }

    /**
     * v8 未満では clip_below が waytype に基づいてデフォルト設定されることを検証する。
     * powerline (128 → 7) → clip_below = false
     */
    public function test_version7_clip_below_defaults_to_false_for_powerline(): void
    {
        $data = pack('v', 7);         // version = 7
        $data .= pack('V', 0).pack('V', 0);  // price sint64
        $data .= pack('V', 0).pack('V', 0);  // maintenance sint64
        $data .= pack('V', 100);     // topspeed
        $data .= pack('V', 500);     // max_weight
        $data .= pack('v', 23880);   // intro_date
        $data .= pack('v', 24240);   // retire_date
        $data .= pack('v', 9999);    // axle_load
        $data .= pack('C', 128);     // waytype = 128 (powerline raw → converted to 7 by applyInternalCorrections)
        $data .= pack('C', 0);       // styp
        $data .= pack('C', 0);       // draw_as_obj
        $data .= pack('c', 0);       // number_of_seasons

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertArrayHasKey('clip_below', $result);
        $this->assertFalse((bool) $result['clip_below']);
    }

    private function makeNode(string $data): Node
    {
        $size = strlen($data);
        $binary = "WAY\x00".pack('v', 0).pack('v', $size).$data;

        return Node::parse(new BinaryReader($binary));
    }
}
