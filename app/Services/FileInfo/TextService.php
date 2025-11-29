<?php

declare(strict_types=1);

namespace App\Services\FileInfo;

final class TextService
{
    public function removeBom(string $text): string
    {
        // Fast path: Check if BOM exists before regex
        if (! str_starts_with($text, "\xEF\xBB\xBF")) {
            return $text;
        }

        return substr($text, 3); // Remove 3-byte UTF-8 BOM
    }

    public function encoding(string $text): string
    {
        // Skip encoding detection for already valid UTF-8 (performance optimization)
        if (mb_check_encoding($text, 'UTF-8')) {
            return trim($text);
        }

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
