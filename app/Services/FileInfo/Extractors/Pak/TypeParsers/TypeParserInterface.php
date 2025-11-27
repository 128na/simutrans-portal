<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;

/**
 * Interface for type-specific pak data parsers
 */
interface TypeParserInterface
{
    /**
     * Parse type-specific data from node
     *
     * @return array<string, mixed>|null
     */
    public function parse(Node $node): ?array;

    /**
     * Check if this parser can handle the given node type
     */
    public function canParse(Node $node): bool;
}
