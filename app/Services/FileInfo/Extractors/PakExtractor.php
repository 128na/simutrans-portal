<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\PakParser;
use Illuminate\Support\Facades\Log;

final readonly class PakExtractor implements Extractor
{
    public function __construct(
        private PakParser $parser,
    ) {}

    #[\Override]
    public function isText(): bool
    {
        return false;
    }

    #[\Override]
    public function getKey(): string
    {
        return 'paks_metadata';
    }

    #[\Override]
    public function isTarget(string $filename): bool
    {
        return str_ends_with($filename, '.pak');
    }

    /**
     * pakバイナリからメタデータを抽出する.
     *
     * @return array<int, array<string, mixed>>
     */
    #[\Override]
    public function extract(string $pakBinary): array
    {
        try {
            return $this->parser->parse($pakBinary);
        } catch (InvalidPakFileException $invalidPakFileException) {
            Log::warning('Failed to parse pak file with new parser', [
                'error' => $invalidPakFileException->getMessage(),
            ]);

            return [];
        }
    }
}
