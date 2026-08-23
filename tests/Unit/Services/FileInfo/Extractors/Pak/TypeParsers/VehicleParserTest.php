<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\VehicleParser;
use Tests\Unit\TestCase;

class VehicleParserTest extends TestCase
{
    private VehicleParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new VehicleParser;
    }

    /**
     * v13 の loading_time が uint32 で読まれることを検証する。
     *
     * v12: loading_time = uint16 (最大 65535)
     * v13: loading_time = uint32 (vehicle_reader.cc:57)
     * uint16 最大値を超える値 (70000) で区別できる。
     */
    public function test_version13_loading_time_is_read_as_uint32(): void
    {
        $data = pack('v', 0x800D);   // version stamp = 13 (bit 15 set)
        $data .= pack('V', 50000);  // price_lo
        $data .= pack('V', 0);      // price_hi
        $data .= pack('v', 10);     // capacity
        $data .= pack('V', 70000);  // loading_time (uint32 — uint16 の最大値 65535 を超える値)
        $data .= pack('v', 80);     // topspeed
        $data .= pack('V', 5000);   // weight
        $data .= pack('v', 10);     // axle_load
        $data .= pack('V', 150);    // power
        $data .= pack('V', 100);    // running_cost_lo
        $data .= pack('V', 0);      // running_cost_hi
        $data .= pack('V', 200);    // maintenance_lo
        $data .= pack('V', 0);      // maintenance_hi
        $data .= pack('v', 23880);  // intro_date (1990*12)
        $data .= pack('v', 24240);  // retire_date (2020*12)
        $data .= pack('v', 64);     // gear
        $data .= pack('C', 1);      // waytype (road)
        $data .= pack('C', 254);    // sound
        $data .= pack('C', 1);      // engine_type (diesel)
        $data .= pack('C', 8);      // len
        $data .= pack('C', 0);      // leader_count
        $data .= pack('C', 0);      // trailer_count
        $data .= pack('C', 0);      // freight_image_type

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(70000, $result['loading_time']);
    }

    /**
     * v13 の全フィールドが正しく読まれることを検証する。
     */
    public function test_version13_parses_all_fields_correctly(): void
    {
        $data = pack('v', 0x800D);   // version stamp = 13 (bit 15 set)
        $data .= pack('V', 50000);  // price_lo
        $data .= pack('V', 0);      // price_hi
        $data .= pack('v', 10);     // capacity
        $data .= pack('V', 500);    // loading_time
        $data .= pack('v', 80);     // topspeed
        $data .= pack('V', 5000);   // weight
        $data .= pack('v', 12);     // axle_load
        $data .= pack('V', 150);    // power
        $data .= pack('V', 100);    // running_cost_lo
        $data .= pack('V', 0);      // running_cost_hi
        $data .= pack('V', 200);    // maintenance_lo
        $data .= pack('V', 0);      // maintenance_hi
        $data .= pack('v', 23880);  // intro_date
        $data .= pack('v', 24240);  // retire_date
        $data .= pack('v', 64);     // gear
        $data .= pack('C', 1);      // waytype (road)
        $data .= pack('C', 254);    // sound
        $data .= pack('C', 1);      // engine_type (diesel)
        $data .= pack('C', 8);      // len
        $data .= pack('C', 2);      // leader_count
        $data .= pack('C', 3);      // trailer_count
        $data .= pack('C', 1);      // freight_image_type

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(50000, $result['price']);
        $this->assertSame(10, $result['capacity']);
        $this->assertSame(500, $result['loading_time']);
        $this->assertSame(80, $result['topspeed']);
        $this->assertSame(5000, $result['weight']);
        $this->assertSame(12, $result['axle_load']);
        $this->assertSame(150, $result['power']);
        $this->assertSame(100, $result['running_cost']);
        $this->assertSame(200, $result['maintenance']);
        $this->assertSame(23880, $result['intro_date']);
        $this->assertSame(24240, $result['retire_date']);
        $this->assertSame(64, $result['gear']);
        $this->assertSame(1, $result['waytype']);
        $this->assertSame(254, $result['sound']);
        $this->assertSame(1, $result['engine_type']);
        $this->assertSame(8, $result['len']);
        $this->assertSame(2, $result['leader_count']);
        $this->assertSame(3, $result['trailer_count']);
        $this->assertSame(1, $result['freight_image_type']);
    }

    /**
     * v1 では intro_date が base-16 (year*16+month) で保存されており、
     * base-12 (year*12+month) に変換される必要がある (vehicle_reader.cc: version<5)。
     * retire_date は保存されず、DEFAULT_RETIRE_YEAR(2999)*12 に変換されたデフォルト値になる。
     */
    public function test_version1_converts_base16_intro_date_to_base12(): void
    {
        $data = pack('v', 0x8001);  // version = 1
        $data .= pack('V', 10000);  // price
        $data .= pack('v', 200);    // capacity
        $data .= pack('v', 100);    // topspeed
        $data .= pack('v', 50);     // weight
        $data .= pack('v', 300);    // power
        $data .= pack('v', 120);    // running_cost
        $data .= pack('v', 31840);  // intro_date raw (1990*16) → (31840/16)*12+(31840%16) = 23880
        $data .= pack('C', 64);     // gear
        $data .= pack('C', 1);      // waytype
        $data .= pack('C', 0);      // sound
        $data .= pack('C', 1);      // leader_count
        $data .= pack('C', 1);      // trailer_count

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(23880, $result['intro_date']);
        $this->assertSame(2999 * 12, $result['retire_date']);
    }

    /**
     * v3 では intro_date/retire_date ともに base-16 → base-12 変換が必要。
     */
    public function test_version3_converts_base16_dates_to_base12(): void
    {
        $data = pack('v', 0x8003);  // version = 3
        $data .= pack('V', 10000);  // price
        $data .= pack('v', 200);    // capacity
        $data .= pack('v', 100);    // topspeed
        $data .= pack('v', 50);     // weight
        $data .= pack('v', 300);    // power
        $data .= pack('v', 120);    // running_cost
        $data .= pack('v', 31840);  // intro_date raw (1990*16) → 23880
        $data .= pack('v', 47984);  // retire_date raw (2999*16) → 35988
        $data .= pack('C', 64);     // gear
        $data .= pack('C', 1);      // waytype
        $data .= pack('C', 0);      // sound
        $data .= pack('C', 1);      // leader_count
        $data .= pack('C', 1);      // trailer_count
        $data .= pack('C', 1);      // engine_type

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(23880, $result['intro_date']);
        $this->assertSame(35988, $result['retire_date']);
    }

    /**
     * v5 以降は既に base-12 のため、変換を適用してはならない (回帰防止)。
     */
    public function test_version5_dates_are_not_converted(): void
    {
        $data = pack('v', 0x8005);  // version = 5
        $data .= pack('V', 10000);  // price
        $data .= pack('v', 200);    // capacity
        $data .= pack('v', 100);    // topspeed
        $data .= pack('v', 50);     // weight
        $data .= pack('v', 300);    // power
        $data .= pack('v', 120);    // running_cost
        $data .= pack('v', 23880);  // intro_date (既に base-12)
        $data .= pack('v', 24240);  // retire_date (既に base-12)
        $data .= pack('C', 64);     // gear
        $data .= pack('C', 1);      // waytype
        $data .= pack('C', 0);      // sound
        $data .= pack('C', 1);      // leader_count
        $data .= pack('C', 1);      // trailer_count
        $data .= pack('C', 1);      // engine_type

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(23880, $result['intro_date']);
        $this->assertSame(24240, $result['retire_date']);
    }

    /**
     * v0 (レガシー、バージョンスタンプなし) は intro_date/retire_date を保存しないため、
     * SimutransDefaults にもとづくデフォルト値 (変換後) になる必要がある
     * (vehicle_reader.cc: version==0 の分岐、および version<5 の共通変換)。
     */
    public function test_version0_defaults_to_simutrans_default_dates(): void
    {
        $data = pack('v', 1);      // waytype = 1 (road, 高位ビット未設定 = v0)
        $data .= pack('v', 100);   // capacity
        $data .= pack('V', 10000); // price
        $data .= pack('v', 80);    // topspeed
        $data .= pack('v', 10);    // weight
        $data .= pack('v', 200);   // power
        $data .= pack('v', 50);    // running_cost

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertNotNull($result);
        $this->assertSame(1900 * 12, $result['intro_date']);
        $this->assertSame(2999 * 12, $result['retire_date']);
    }

    /**
     * v14 (未対応バージョン) は例外を投げる (回帰防止: 将来バージョンのサイレント無視を防ぐ)。
     * 修正前は default => [] で無警告のまま空データを返していた。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported vehicle version: 14 (max known: 13)');

        $data = pack('v', 0x8000 | 14);
        $this->parser->parse($this->makeNode($data));
    }

    private function makeNode(string $data): Node
    {
        $size = strlen($data);
        $binary = 'VHCL'.pack('v', 0).pack('v', $size).$data;

        return Node::parse(new BinaryReader($binary));
    }
}
