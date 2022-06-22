<?php

namespace App\Services\FileInfo\Extractors;

use App\Services\Service;

class PakExtractor extends Service implements Extractor
{
    public function __construct()
    {
    }

    public function getKey(): string
    {
        return 'paks';
    }

    public function isTarget(string $filename): bool
    {
        return str_ends_with($filename, '.pak');
    }

    /**
     * pakバイナリからアドオン名を抽出する.
     *
     * @return string[]
     */
    public function extract(string $pakBinary): array
    {
        $pak = app(PakBinary::class, ['binary' => $pakBinary]);
        $nameKey = pack('H*', '948C');
        $textKey = pack('H*', '54455854');
        $names = [];
        while (!$pak->eof()) {
            $pak->seekUntil($nameKey); // objへシーク
            $pak->seekUntil($textKey); // 最初のテキストノード（＝アドオン名）へシーク
            $pak->seek(6);
            if (!$pak->eof()) {
                $len = $this->toNumber($pak->readChar(2)); // 文字数
                $names[] = $pak->readChar($len - 1);
            }
        }

        return $names;
    }

    private function toNumber(string $binary): int
    {
        $chars = array_reverse(str_split($binary, 2));

        $result = 0;
        $order = 0;
        foreach ($chars as $char) {
            $unpacked = unpack('v', $char);
            $result += array_shift($unpacked) * (16 ** $order);
            ++$order;
        }

        return $result;
    }
}
