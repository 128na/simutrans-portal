<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

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

    private function makeNode(string $data): Node
    {
        $size = strlen($data);
        $binary = 'VHCL' . pack('v', 0) . pack('v', $size) . $data;

        return Node::parse(new BinaryReader($binary));
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
}
