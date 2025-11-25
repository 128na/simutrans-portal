<?php

declare(strict_types=1);

namespace App\Services\FileInfo;

final class TextService
{
    public function removeBom(string $text): string
    {
        $bom = pack('H*', 'EFBBBF');

        return preg_replace(sprintf('/^%s/', $bom), '', $text) ?? $text;
    }

    public function encoding(string $text): string
    {
        $enc = mb_detect_encoding($text, ['UTF-8', 'SJIS', 'EUC-JP']) ?: 'UTF-8';
        $encoded = mb_convert_encoding(trim($text), 'UTF-8', $enc);
        if ($encoded === false) {
            throw new InvalidEncodingException($text);
        }

        $result = json_encode([$encoded]);

        if ($result === false) {
            throw new InvalidEncodingException($text);
        }

        return $encoded;
    }
}
