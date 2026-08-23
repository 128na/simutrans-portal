<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\FactoryParser;
use Tests\Unit\TestCase;

class FactoryParserTest extends TestCase
{
    private FactoryParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new FactoryParser;
    }

    /**
     * v0 (レガシー、バージョンスタンプなし) の基本フィールドを検証する。
     */
    public function test_version0_parses_basic_fields(): void
    {
        $payload = pack('v', 1);      // placement (firstUint16, 高位ビット未設定 = v0)
        $payload .= pack('v', 0);     // always-zero field
        $payload .= pack('v', 0x0064); // productivity (0x8000 が付与される)
        $payload .= pack('v', 50);    // range
        $payload .= pack('v', 100);   // distribution_weight
        $payload .= pack('v', 3);     // color
        $payload .= pack('v', 1);     // supplier_count
        $payload .= pack('v', 1);     // product_count

        $result = $this->parser->parse($this->makeFactoryNode(0, $payload));

        $this->assertSame(0, $result['version']);
        $this->assertSame(1, $result['placement']); // firstUint16
        $this->assertSame(0x0064 | 0x8000, $result['productivity']);
        $this->assertSame(50, $result['range']);
        $this->assertSame([], $result['input']);
        $this->assertSame([], $result['output']);
        $this->assertSame([], $result['field_groups']);
        $this->assertSame([], $result['smoke']);
    }

    /**
     * v6 は v5 の全フィールドに加えて electricity_producer (uint8) を
     * mail_demand の直後・sound_interval の手前に追加する
     * (factory_reader.cc: version==6)。
     */
    public function test_version6_reads_electricity_producer_and_smoke_fields(): void
    {
        $payload = pack('v', 1);      // placement
        $payload .= pack('v', 5000);  // productivity
        $payload .= pack('v', 50);    // range
        $payload .= pack('v', 100);   // distribution_weight
        $payload .= pack('C', 3);     // color
        $payload .= pack('C', 2);     // fields (raw slot count)
        $payload .= pack('v', 1);     // supplier_count
        $payload .= pack('v', 1);     // product_count
        $payload .= pack('v', 16);    // pax_level
        $payload .= pack('v', 5000);  // expand_probability raw
        $payload .= pack('v', 1);     // expand_minimum
        $payload .= pack('v', 2);     // expand_range
        $payload .= pack('v', 3);     // expand_times
        $payload .= pack('v', 256);   // electric_boost
        $payload .= pack('v', 0);     // pax_boost
        $payload .= pack('v', 0);     // mail_boost
        $payload .= pack('v', 65535); // electric_demand
        $payload .= pack('v', 65535); // pax_demand
        $payload .= pack('v', 65535); // mail_demand
        $payload .= pack('C', 1);     // electricity_producer (NEW in v6)
        $payload .= pack('V', 1000);  // sound_interval
        $payload .= pack('c', 5);     // sound_id
        $payload .= pack('c', 4);     // smokerotations
        $payload .= str_repeat("\x00", 32); // smoketile/smokeoffset (unused, skipped)
        $payload .= pack('v', 10);    // smokeuplift
        $payload .= pack('v', 20);    // smokelifetime

        $result = $this->parser->parse($this->makeFactoryNode(6, $payload));

        $this->assertSame(6, $result['version']);
        $this->assertSame(1, $result['electricity_producer']);
        $this->assertSame(1000, $result['sound_interval']);
        $this->assertSame(5, $result['sound_id']);
        $this->assertSame(4, $result['smokerotations']);
        $this->assertSame(10, $result['smokeuplift']);
        $this->assertSame(20, $result['smokelifetime']);
        // 5000/10000 の確率を256回合成した値 (rescale_probability) は元の5000より大きい
        $this->assertGreaterThan(5000, $result['expand_probability']);
        $this->assertLessThanOrEqual(10000, $result['expand_probability']);
    }

    /**
     * FFIE (field group) v3 + FFCL (field class) 子ノードの抽出を検証する。
     */
    public function test_extracts_field_group_v3_with_field_classes(): void
    {
        $fieldClassPayload = pack('C', 1);     // snow_image
        $fieldClassPayload .= pack('v', 200);  // production_per_field
        $fieldClassPayload .= pack('v', 500);  // storage_capacity
        $fieldClassPayload .= pack('v', 1000); // spawn_weight
        $fieldClassNode = $this->encodeNode('FFCL', pack('v', 0x8001).$fieldClassPayload);

        $fieldGroupPayload = pack('v', 5000); // probability raw
        $fieldGroupPayload .= pack('v', 10);  // max_fields
        $fieldGroupPayload .= pack('v', 2);   // min_fields
        $fieldGroupPayload .= pack('v', 1);   // start_fields
        $fieldGroupPayload .= pack('v', 1);   // field_classes count
        $fieldGroupNode = $this->encodeNode('FFIE', pack('v', 0x8003).$fieldGroupPayload, [$fieldClassNode]);

        $factoryPayload = $this->minimalV1Payload();
        $node = $this->makeFactoryNode(1, $factoryPayload, [$fieldGroupNode]);

        $result = $this->parser->parse($node);

        $this->assertCount(1, $result['field_groups']);
        $group = $result['field_groups'][0];
        $this->assertSame(3, $group['version']);
        $this->assertSame(10, $group['max_fields']);
        $this->assertSame(2, $group['min_fields']);
        $this->assertSame(1, $group['start_fields']);
        $this->assertGreaterThan(5000, $group['probability']);

        $this->assertCount(1, $group['classes']);
        $this->assertSame(1, $group['classes'][0]['snow_image']);
        $this->assertSame(200, $group['classes'][0]['production_per_field']);
        $this->assertSame(500, $group['classes'][0]['storage_capacity']);
        $this->assertSame(1000, $group['classes'][0]['spawn_weight']);
    }

    /**
     * FFIE (field group) v1 は子ノードなしでクラスデータを埋め込む
     * 最古形式 (油田等) を検証する。
     */
    public function test_extracts_field_group_v1_with_embedded_class(): void
    {
        $fieldGroupPayload = pack('C', 2);    // snow_image
        $fieldGroupPayload .= pack('v', 3000); // probability raw
        $fieldGroupPayload .= pack('v', 150);  // production_per_field
        $fieldGroupPayload .= pack('v', 8);    // max_fields
        $fieldGroupPayload .= pack('v', 1);    // min_fields
        $fieldGroupNode = $this->encodeNode('FFIE', pack('v', 0x8001).$fieldGroupPayload);

        $node = $this->makeFactoryNode(1, $this->minimalV1Payload(), [$fieldGroupNode]);

        $result = $this->parser->parse($node);

        $this->assertCount(1, $result['field_groups']);
        $group = $result['field_groups'][0];
        $this->assertSame(1, $group['version']);
        $this->assertSame(8, $group['max_fields']);
        $this->assertSame(1, $group['min_fields']);
        $this->assertNull($group['start_fields']);
        $this->assertCount(1, $group['classes']);
        $this->assertSame(2, $group['classes'][0]['snow_image']);
        $this->assertSame(150, $group['classes'][0]['production_per_field']);
        $this->assertSame(0, $group['classes'][0]['storage_capacity']);
        $this->assertSame(1000, $group['classes'][0]['spawn_weight']);
    }

    /**
     * FSMO (smoke) 子ノードの抽出を検証する。バージョンスタンプを持たず、
     * 符号付き16bit座標2組 (pos, offset) を読む (factory_smoke_reader.cc)。
     */
    public function test_extracts_smoke_position_with_negative_offsets(): void
    {
        $smokePayload = pack('s', 10);   // pos.x
        $smokePayload .= pack('s', -5);  // pos.y (負値)
        $smokePayload .= pack('s', -20); // offset.x (負値)
        $smokePayload .= pack('s', 15);  // offset.y
        $smokePayload .= pack('s', 0);   // smoke speed (未使用、読み捨て)
        $smokeNode = $this->encodeNode('FSMO', $smokePayload);

        $node = $this->makeFactoryNode(1, $this->minimalV1Payload(), [$smokeNode]);

        $result = $this->parser->parse($node);

        $this->assertCount(1, $result['smoke']);
        $this->assertSame(10, $result['smoke'][0]['pos_x']);
        $this->assertSame(-5, $result['smoke'][0]['pos_y']);
        $this->assertSame(-20, $result['smoke'][0]['offset_x']);
        $this->assertSame(15, $result['smoke'][0]['offset_y']);
    }

    /**
     * v7 (未対応バージョン) は例外を投げる (回帰防止: 将来バージョンのサイレント誤読を防ぐ)。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported factory version: 7 (max known: 6)');

        $this->parser->parse($this->makeFactoryNode(7, ''));
    }

    /**
     * FFIE (field group) の未対応バージョン (4) はその他の工場データ
     * (productivity 等) を巻き込まず、field_groups のみ空配列に縮退する
     * (FFIE/FFCL は本体の FACT バージョンとは独立してバージョニングされるため)。
     */
    public function test_unsupported_field_group_version_degrades_gracefully(): void
    {
        $fieldGroupNode = $this->encodeNode('FFIE', pack('v', 0x8000 | 4));
        $node = $this->makeFactoryNode(1, $this->minimalV1Payload(), [$fieldGroupNode]);

        $result = $this->parser->parse($node);

        $this->assertSame([], $result['field_groups']);
        $this->assertSame(1000, $result['productivity']);
    }

    /**
     * FFCL (field class) の未対応バージョン (2) も同様に factory 全体を
     * 巻き込まず、field_groups のみ空配列に縮退する。
     */
    public function test_unsupported_field_class_version_degrades_gracefully(): void
    {
        $fieldClassNode = $this->encodeNode('FFCL', pack('v', 0x8000 | 2));
        // v3 field group payload: probability, max_fields, min_fields, start_fields, field_classes count
        $fieldGroupPayload = pack('v', 5000).pack('v', 10).pack('v', 2).pack('v', 1).pack('v', 1);
        $fieldGroupNode = $this->encodeNode('FFIE', pack('v', 0x8003).$fieldGroupPayload, [$fieldClassNode]);
        $node = $this->makeFactoryNode(1, $this->minimalV1Payload(), [$fieldGroupNode]);

        $result = $this->parser->parse($node);

        $this->assertSame([], $result['field_groups']);
        $this->assertSame(1000, $result['productivity']);
    }

    /**
     * @return string v1 factory の最小ペイロード (placement〜pax_level)
     */
    private function minimalV1Payload(): string
    {
        $payload = pack('v', 1);      // placement
        $payload .= pack('v', 1000);  // productivity
        $payload .= pack('v', 50);    // range
        $payload .= pack('v', 100);   // distribution_weight
        $payload .= pack('v', 3);     // color (v1 は uint16)
        $payload .= pack('v', 1);     // supplier_count
        $payload .= pack('v', 1);     // product_count
        $payload .= pack('v', 16);    // pax_level

        return $payload;
    }

    /**
     * @param  array<string>  $children  encodeNode() で作った子ノードのバイナリ列
     */
    private function makeFactoryNode(int $version, string $payload, array $children = []): Node
    {
        $versionedPayload = $version === 0 ? $payload : pack('v', 0x8000 | $version).$payload;

        return $this->decodeNode($this->encodeNode('FACT', $versionedPayload, $children));
    }

    /**
     * ノード1つをバイナリにエンコードする (type[4] + children[2] + size[2] + data + 子ノード群)。
     *
     * @param  array<string>  $children  encodeNode() で作った子ノードのバイナリ列
     */
    private function encodeNode(string $type, string $data, array $children = []): string
    {
        $type = str_pad($type, 4, "\x00");

        return $type.pack('v', count($children)).pack('v', strlen($data)).$data.implode('', $children);
    }

    private function decodeNode(string $binary): Node
    {
        return Node::parse(new BinaryReader($binary));
    }
}
