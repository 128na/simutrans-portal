<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\SignParser;
use Tests\Unit\TestCase;

class SignParserTest extends TestCase
{
    private SignParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new SignParser;
    }

    /**
     * v1 は intro_date/retire_date を保存しないため、本家の
     * DEFAULT_INTRO_YEAR(1900)/DEFAULT_RETIRE_YEAR(2999) にもとづく
     * デフォルト値になる必要がある (roadsign_reader.cc)。
     *
     * バグ: 修正前は intro_date=0, retire_date=65535 という無関係な値が
     * 使われており、引退年月が "5461/04" のような無意味な日付になっていた。
     */
    public function test_version1_defaults_to_simutrans_default_dates(): void
    {
        $payload = pack('v', 100);   // min_speed
        $payload .= pack('C', 0);    // flags

        $result = $this->parser->parse($this->makeNode(1, $payload));

        $this->assertSame(1900 * 12, $result['intro_date']);
        $this->assertSame(2999 * 12, $result['retire_date']);
        $this->assertSame(50000, $result['price']);
    }

    /**
     * v2 も同様に intro_date/retire_date を保存しないため、
     * 同じデフォルト値になる必要がある。
     */
    public function test_version2_defaults_to_simutrans_default_dates(): void
    {
        $payload = pack('v', 100);    // min_speed
        $payload .= pack('V', 30000); // price
        $payload .= pack('C', 0);     // flags

        $result = $this->parser->parse($this->makeNode(2, $payload));

        $this->assertSame(1900 * 12, $result['intro_date']);
        $this->assertSame(2999 * 12, $result['retire_date']);
        $this->assertSame(30000, $result['price']);
    }

    /**
     * v3 以降は intro_date/retire_date をバイナリから直接読むため、
     * デフォルト値による上書きは発生しない (回帰防止)。
     */
    public function test_version3_reads_dates_from_binary(): void
    {
        $payload = pack('v', 100);    // min_speed
        $payload .= pack('V', 30000); // price
        $payload .= pack('C', 0);     // flags
        $payload .= pack('C', 2);     // waytype
        $payload .= pack('v', 23880); // intro_date
        $payload .= pack('v', 24240); // retire_date

        $result = $this->parser->parse($this->makeNode(3, $payload));

        $this->assertSame(23880, $result['intro_date']);
        $this->assertSame(24240, $result['retire_date']);
    }

    /**
     * v0 (レガシー、バージョンスタンプなし) は未対応として例外を投げる。
     */
    public function test_version0_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported roadsign version: 0 (max known: 6)');

        // 高位ビット未設定の生データ (legacy v0 相当)
        $this->parser->parse($this->makeNode(0, pack('v', 100)));
    }

    /**
     * v7 (未対応バージョン) は例外を投げる (回帰防止)。
     */
    public function test_unsupported_version_throws(): void
    {
        $this->expectException(InvalidPakFileException::class);
        $this->expectExceptionMessage('Unsupported roadsign version: 7 (max known: 6)');

        $this->parser->parse($this->makeNode(7, ''));
    }

    private function makeNode(int $version, string $payload): Node
    {
        $data = $version === 0 ? $payload : pack('v', 0x8000 | $version).$payload;
        $size = strlen($data);
        $binary = 'SIGN'.pack('v', 0).pack('v', $size).$data;

        return Node::parse(new BinaryReader($binary));
    }
}
