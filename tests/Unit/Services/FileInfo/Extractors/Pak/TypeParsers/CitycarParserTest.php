<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\CitycarParser;
use Tests\Unit\TestCase;

class CitycarParserTest extends TestCase
{
    private CitycarParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new CitycarParser;
    }

    /**
     * v0 (レガシー、バージョンスタンプなし) は intro_date/retire_date を保存しないため、
     * 本家 citycar_reader.cc の DEFAULT_INTRO_YEAR(1900)/DEFAULT_RETIRE_YEAR(2999) に
     * もとづくデフォルト値になる必要がある。
     */
    public function test_version0_defaults_to_simutrans_default_dates(): void
    {
        // firstUint16 (高位ビット未設定) = distribution_weight
        $data = pack('v', 50);

        $result = $this->parser->parse($this->makeNode($data));

        $this->assertSame(50, $result['distribution_weight']);
        $this->assertSame(1900 * 12, $result['intro_date']);
        $this->assertSame(2999 * 12, $result['retire_date']);
    }

    /**
     * v1 は intro_date/retire_date を (raw/16)*12+(raw%12) で変換する
     * (citycar_reader.cc: 他パーサー(Way等)の %16 ベースの変換とは異なり、
     * 本家の実装自体が剰余を %12 で取る点に注意。意図的にこの仕様に合わせている)。
     */
    public function test_version1_converts_base16_dates_to_base12(): void
    {
        $payload = pack('v', 50);     // distribution_weight
        $payload .= pack('v', 1600);  // topspeed raw (÷16 = 100 km/h)
        $payload .= pack('v', 31840); // intro_date raw → (31840/16)*12+(31840%12) = 1990*12+4 = 23884
        $payload .= pack('v', 47984); // retire_date raw → (47984/16)*12+(47984%12) = 2999*12+8 = 35996

        $result = $this->parser->parse($this->makeVersionedNode(1, $payload));

        $this->assertSame(100, $result['topspeed']);
        $this->assertSame(23884, $result['intro_date']);
        $this->assertSame(35996, $result['retire_date']);
    }

    /**
     * v2 は intro_date/retire_date を直接 base-12 で保存する (変換不要)。
     */
    public function test_version2_reads_dates_directly(): void
    {
        $payload = pack('v', 50);     // distribution_weight
        $payload .= pack('v', 1600);  // topspeed raw
        $payload .= pack('v', 23880); // intro_date (既に base-12)
        $payload .= pack('v', 24240); // retire_date (既に base-12)

        $result = $this->parser->parse($this->makeVersionedNode(2, $payload));

        $this->assertSame(23880, $result['intro_date']);
        $this->assertSame(24240, $result['retire_date']);
    }

    /**
     * v3 (未対応バージョン) は例外を投げる (回帰防止)。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported citycar version: 3 (max known: 2)');

        $this->parser->parse($this->makeVersionedNode(3, ''));
    }

    private function makeNode(string $data): Node
    {
        // legacy (v0): 高位ビットを立てない生のfirstUint16をそのまま使う
        $size = strlen($data);
        $binary = 'CCAR'.pack('v', 0).pack('v', $size).$data;

        return Node::parse(new BinaryReader($binary));
    }

    private function makeVersionedNode(int $version, string $payload): Node
    {
        $data = pack('v', 0x8000 | $version).$payload;

        return $this->makeNode($data);
    }
}
