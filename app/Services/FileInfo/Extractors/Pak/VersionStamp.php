<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Extracts versioned format stamp from pak binary data.
 *
 * Pak nodes store a version stamp as the first uint16:
 * - If the high bit (0x8000) is set: bits 0-14 are the version number (versioned format)
 * - Otherwise: the entire uint16 is a legacy field value (version 0)
 */
final class VersionStamp
{
    public function __construct(
        public readonly int $version,
        public readonly int $firstUint16,
        public readonly bool $isVersioned,
    ) {}

    public static function from(string $data, int $offset = 0): self
    {
        $result = unpack('v', substr($data, $offset, 2));
        if ($result === false) {
            return new self(version: 0, firstUint16: 0, isVersioned: false);
        }
        $v = $result[1];
        $isVersioned = ($v & 0x8000) !== 0;

        return new self(
            version: $isVersioned ? ($v & 0x7FFF) : 0,
            firstUint16: $v,
            isVersioned: $isVersioned,
        );
    }
}
