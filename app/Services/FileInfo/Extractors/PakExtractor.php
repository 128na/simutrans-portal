<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\PakParser;
use Illuminate\Support\Facades\Log;

final readonly class PakExtractor implements Extractor
{
    public function __construct(
        private PakParser $parser,
    ) {}

    #[\Override]
    public function isText(): bool
    {
        return false;
    }

    #[\Override]
    public function getKey(): string
    {
        return 'paks';
    }

    #[\Override]
    public function isTarget(string $filename): bool
    {
        return str_ends_with($filename, '.pak');
    }

    /**
     * pakバイナリからアドオン名とメタデータを抽出する.
     *
     * @return array{names: array<int, string>, metadata: array<int, array<string, mixed>>}
     */
    #[\Override]
    public function extract(string $pakBinary): array
    {
        try {
            return $this->parser->parse($pakBinary);
        } catch (InvalidPakFileException $invalidPakFileException) {
            Log::warning('Failed to parse pak file with new parser, falling back to legacy parser', [
                'error' => $invalidPakFileException->getMessage(),
            ]);

            return [
                'names' => $this->fallbackExtract($pakBinary),
                'metadata' => [],
            ];
        }
    }

    /**
     * レガシーパーサー（フォールバック用）
     *
     * @return string[]
     */
    private function fallbackExtract(string $pakBinary): array
    {
        /** @var PakBinary */
        $pak = app(PakBinary::class, ['binary' => $pakBinary]);
        $nameKey = pack('H*', '948C');
        $textKey = pack('H*', '54455854');
        $names = [];
        while (! $pak->eof()) {
            $pak->seekUntil($nameKey); // objへシーク
            $pak->seekUntil($textKey); // 最初のテキストノード（＝アドオン名）へシーク
            $pak->seek(6);
            if (! $pak->eof()) {
                $len = $this->toNumber($pak->readChar(2)); // 文字数
                $names[] = $pak->readChar($len - 1);
            }
        }

        return $names;
    }

    private function toNumber(string $binary): int
    {
        $chars = array_reverse(mb_str_split($binary, 2));

        $result = 0;
        $order = 0;
        foreach ($chars as $char) {
            $unpacked = unpack('v', $char) ?: [];
            $result += (array_shift($unpacked) ?: 0) * (16 ** $order);
            $order++;
        }

        return $result;
    }
}
