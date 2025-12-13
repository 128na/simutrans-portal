<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Extract text from TEXT node
 */
class TextNodeExtractor
{
    /**
     * Extract null-terminated string from node data
     */
    public static function extract(Node $node): string
    {
        $nullPos = strpos($node->data, "\0");
        if ($nullPos === false) {
            return $node->data;
        }

        return substr($node->data, 0, $nullPos);
    }
}
