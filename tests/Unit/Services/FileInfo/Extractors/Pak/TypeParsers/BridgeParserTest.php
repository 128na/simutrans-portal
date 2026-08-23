<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\BridgeParser;
use Tests\Unit\TestCase;

class BridgeParserTest extends TestCase
{
    private BridgeParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new BridgeParser;
    }

    /**
     * v11 で clip_below フィールドが読み取れることを検証する。
     *
     * v10: clip_below なし (waytype で自動設定)
     * v11: clip_below を明示的に読む (bridge_reader.cc:162)
     */
    public function test_version11_parses_clip_below_from_binary(): void
    {
        $data = pack('v', 0x800B);   // version stamp = 11 (bit 15 set)
        $data .= pack('v', 100);    // topspeed
        $data .= pack('P', 200000); // price (sint64)
        $data .= pack('P', 1000);   // maintenance (sint64)
        $data .= pack('C', 1);      // waytype (road)
        $data .= pack('C', 2);      // pillars_every
        $data .= pack('C', 5);      // max_length
        $data .= pack('v', 23880);  // intro_date
        $data .= pack('v', 24240);  // retire_date
        $data .= pack('C', 0);      // pillars_asymmetric
        $data .= pack('v', 15);     // axle_load
        $data .= pack('C', 3);      // max_height
        $data .= pack('C', 1);      // clip_below = true
        $data .= pack('C', 2);      // number_of_seasons

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertArrayHasKey('clip_below', $result);
        $this->assertTrue($result['clip_below']);
    }

    /**
     * v11 の全フィールドが正しく読まれることを検証する。
     */
    public function test_version11_parses_all_fields_correctly(): void
    {
        $data = pack('v', 0x800B);   // version stamp = 11
        $data .= pack('v', 120);    // topspeed
        $data .= pack('P', 500000); // price (sint64)
        $data .= pack('P', 2000);   // maintenance (sint64)
        $data .= pack('C', 2);      // waytype (track)
        $data .= pack('C', 4);      // pillars_every
        $data .= pack('C', 10);     // max_length
        $data .= pack('v', 23880);  // intro_date
        $data .= pack('v', 24240);  // retire_date
        $data .= pack('C', 1);      // pillars_asymmetric = true
        $data .= pack('v', 20);     // axle_load
        $data .= pack('C', 5);      // max_height
        $data .= pack('C', 0);      // clip_below = false
        $data .= pack('C', 1);      // number_of_seasons

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(11, $result['version']);
        $this->assertSame(120, $result['topspeed']);
        $this->assertSame(500000, $result['price']);
        $this->assertSame(2000, $result['maintenance']);
        $this->assertSame(2, $result['waytype']);
        $this->assertSame(4, $result['pillars_every']);
        $this->assertSame(10, $result['max_length']);
        $this->assertSame(23880, $result['intro_date']);
        $this->assertSame(24240, $result['retire_date']);
        $this->assertTrue($result['pillars_asymmetric']);
        $this->assertSame(20, $result['axle_load']);
        $this->assertSame(5, $result['max_height']);
        $this->assertFalse($result['clip_below']);
        $this->assertSame(1, $result['number_of_seasons']);
    }

    /**
     * v10 (v11未満) は clip_below をバイナリから読まず、waytype から自動計算される
     * ことを検証する (bridge_reader.cc: `if (version < 11) clip_below = wtyp != powerline_wt;`)。
     * road (waytype=1, powerline_wt(128)ではない) → clip_below = true
     */
    public function test_version10_computes_clip_below_from_waytype_for_road(): void
    {
        $data = pack('v', 0x800A);   // version stamp = 10
        $data .= pack('v', 100);    // topspeed
        $data .= pack('P', 200000); // price (sint64)
        $data .= pack('P', 1000);   // maintenance (sint64)
        $data .= pack('C', 1);      // waytype (road)
        $data .= pack('C', 2);      // pillars_every
        $data .= pack('C', 5);      // max_length
        $data .= pack('v', 23880);  // intro_date
        $data .= pack('v', 24240);  // retire_date
        $data .= pack('C', 0);      // pillars_asymmetric
        $data .= pack('v', 15);     // axle_load
        $data .= pack('C', 3);      // max_height
        $data .= pack('C', 1);      // number_of_seasons

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertArrayHasKey('clip_below', $result);
        $this->assertTrue($result['clip_below']);
    }

    /**
     * v10 で waytype=128 (powerline_wt) の場合は clip_below = false になる。
     */
    public function test_version10_computes_clip_below_false_for_powerline(): void
    {
        $data = pack('v', 0x800A);   // version stamp = 10
        $data .= pack('v', 100);    // topspeed
        $data .= pack('P', 200000); // price (sint64)
        $data .= pack('P', 1000);   // maintenance (sint64)
        $data .= pack('C', 128);    // waytype (powerline_wt)
        $data .= pack('C', 0);      // pillars_every
        $data .= pack('C', 0);      // max_length
        $data .= pack('v', 23880);  // intro_date
        $data .= pack('v', 24240);  // retire_date
        $data .= pack('C', 0);      // pillars_asymmetric
        $data .= pack('v', 15);     // axle_load
        $data .= pack('C', 0);      // max_height
        $data .= pack('C', 1);      // number_of_seasons

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertArrayHasKey('clip_below', $result);
        $this->assertFalse($result['clip_below']);
    }

    private function makeNode(string $data): Node
    {
        $size = strlen($data);
        $binary = 'BRDG'.pack('v', 0).pack('v', $size).$data;

        return Node::parse(new BinaryReader($binary));
    }
}
