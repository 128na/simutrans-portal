<?php

namespace App\Services\FileInfo;

use App\Services\Service;

class TextService extends Service
{
    public function removeBom(string $text): string
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);

        return $text;
    }

    public function encoding(string $text): string
    {
        $enc = mb_detect_encoding($text, ['SJIS', 'EUC-JP', 'UTF-8']);
        $encoded = mb_convert_encoding(trim($text), 'UTF-8', $enc);
        $result = json_encode([$encoded]);

        if ($result === false) {
            throw new InvalidEncodingException($text);
        }

        return $encoded;
    }
}
