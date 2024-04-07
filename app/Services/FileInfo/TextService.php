<?php

declare(strict_types=1);

namespace App\Services\FileInfo;

class TextService
{
    public function removeBom(string $text): string
    {
        $bom = pack('H*', 'EFBBBF');

        return preg_replace(sprintf('/^%s/', $bom), '', $text) ?? $text;
    }

    public function encoding(string $text): string
    {
        $enc = mb_detect_encoding($text, ['UTF-8', 'SJIS', 'EUC-JP']) ?: null;
        $encoded = mb_convert_encoding(trim($text), 'UTF-8', $enc);
        $result = json_encode([$encoded]);

        if ($result === false) {
            throw new InvalidEncodingException($text);
        }

        return $encoded;
    }
}
