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
        $text = mb_convert_encoding(trim($text), 'UTF-8');
        $result = json_encode([$text]);

        if ($result === false) {
            throw new InvalidEncodingException($text);
        }

        return $text;
    }
}
