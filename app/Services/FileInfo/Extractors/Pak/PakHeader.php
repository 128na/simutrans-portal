<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

use App\Exceptions\InvalidPakFileException;

/**
 * Pak file header information
 */
final readonly class PakHeader
{
    private function __construct(
        public string $compilerVersion,
        public int $compilerVersionCode,
    ) {}

    public static function parse(BinaryReader $reader): self
    {
        // Read "Simutrans object file\n" (22 bytes)
        $magic = $reader->readString(22);
        if ($magic !== "Simutrans object file\n") {
            throw InvalidPakFileException::invalidHeader();
        }

        // Read "Compiled with SimObjects X.X.X\n" until we find \x1A
        $versionLine = '';
        while ($reader->hasMore()) {
            $char = $reader->readString(1);
            if ($char === "\x1A") {
                break;
            }

            $versionLine .= $char;
        }

        // Extract version from "Compiled with SimObjects X.X.X\n"
        if (! str_starts_with($versionLine, 'Compiled with SimObjects ')) {
            throw InvalidPakFileException::invalidHeader();
        }

        // Remove prefix and trim (removes \n at the end)
        $compilerVersion = trim(str_replace('Compiled with SimObjects ', '', $versionLine));

        // Read compiler version code (uint32, little-endian)
        $compilerVersionCode = $reader->readUint32LE();

        return new self($compilerVersion, $compilerVersionCode);
    }
}
