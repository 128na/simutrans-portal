<?php

declare(strict_types=1);

namespace App\Services\FileInfo;

use App\Services\Service;

class TextService extends Service
{
    public function removeBom(string $text): string
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text) ?? $text;

        return $text;
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
