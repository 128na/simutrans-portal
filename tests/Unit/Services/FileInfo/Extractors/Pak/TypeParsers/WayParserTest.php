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
     * powerline (128, そのまま) → clip_below = false
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
        $data .= pack('C', 128);     // waytype = 128 (powerline_wt, applyInternalCorrectionsで変化しない)
        $data .= pack('C', 0);       // styp
        $data .= pack('C', 0);       // draw_as_obj
        $data .= pack('c', 0);       // number_of_seasons

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(128, $result['waytype']);
        $this->assertArrayHasKey('clip_below', $result);
        $this->assertFalse((bool) $result['clip_below']);
    }

    /**
     * 現行の waytype_t/systemtype_t (simtypes.h) では tram_wt=7, track_wt=2 であり、
     * raw waytype=7 (市電) は waytype=2 (track) + styp=7 (tram) に補正される。
     */
    public function test_tram_waytype_is_corrected_to_track_with_tram_systemtype(): void
    {
        $data = pack('v', 2);        // version = 2
        $data .= pack('V', 10000);   // price
        $data .= pack('V', 800);     // maintenance
        $data .= pack('V', 100);     // topspeed
        $data .= pack('V', 500);     // max_weight
        $data .= pack('v', 23880);   // intro_date
        $data .= pack('v', 24240);   // retire_date
        $data .= pack('C', 7);       // waytype = 7 (tram_wt raw)
        $data .= pack('C', 0);       // styp

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(2, $result['waytype']); // track_wt
        $this->assertSame(7, $result['styp']);    // type_tram
    }

    /**
     * raw waytype=5 (現行 enum ではモノレール) は市電と誤認識されて道路に
     * 変換されてはならない (旧番号体系を前提とした補正テーブルのバグの回帰防止)。
     */
    public function test_monorail_waytype_is_not_corrupted_to_road(): void
    {
        $data = pack('v', 2);        // version = 2
        $data .= pack('V', 10000);   // price
        $data .= pack('V', 800);     // maintenance
        $data .= pack('V', 100);     // topspeed
        $data .= pack('V', 500);     // max_weight
        $data .= pack('v', 23880);   // intro_date
        $data .= pack('v', 24240);   // retire_date
        $data .= pack('C', 5);       // waytype = 5 (monorail_wt raw)
        $data .= pack('C', 0);       // styp (flat)

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(5, $result['waytype']); // monorail_wt のまま
        $this->assertSame(0, $result['styp']);
    }

    /**
     * styp=5 かつ waytype=track_wt(2) の組み合わせは、モノレール専用トラックの
     * 旧表現として waytype=monorail_wt(5) + styp=flat(0) に補正される。
     */
    public function test_old_monorail_track_representation_is_corrected(): void
    {
        $data = pack('v', 2);        // version = 2
        $data .= pack('V', 10000);   // price
        $data .= pack('V', 800);     // maintenance
        $data .= pack('V', 100);     // topspeed
        $data .= pack('V', 500);     // max_weight
        $data .= pack('v', 23880);   // intro_date
        $data .= pack('v', 24240);   // retire_date
        $data .= pack('C', 2);       // waytype = 2 (track_wt)
        $data .= pack('C', 5);       // styp = 5 (旧モノレール表現)

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(5, $result['waytype']); // monorail_wt
        $this->assertSame(0, $result['styp']);    // type_flat
    }

    private function makeNode(string $data): Node
    {
        $size = strlen($data);
        $binary = "WAY\x00".pack('v', 0).pack('v', $size).$data;

        return Node::parse(new BinaryReader($binary));
    }
}
