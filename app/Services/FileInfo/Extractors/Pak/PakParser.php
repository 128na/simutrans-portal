<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Simutrans .pak ファイルパーサー
 *
 * Simutrans のバイナリ形式を参考に独自実装。
 * 参考: https://github.com/128na/simutrans/tree/OTRP-KUTAv6/descriptor
 */
final readonly class PakParser
{
    /**
     * Parse pak file and extract metadata
     *
     * @return array{names: array<int, string>, metadata: array<int, array{name: string, copyright: string|null, objectType: string, compilerVersionCode: int}>}
     */
    public function parse(string $binary): array
    {
        $reader = new BinaryReader($binary);

        // Parse header
        $header = PakHeader::parse($reader);

        // Parse root node
        $root = Node::parse($reader);

        // Extract metadata from nodes
        $metadata = $this->extractMetadata($root, $header->compilerVersionCode);

        // Extract names for backward compatibility
        $names = array_map(fn(array $m): string => $m['name'], $metadata);

        return [
            'names' => $names,
            'metadata' => $metadata,
        ];
    }

    /**
     * Extract metadata from node tree recursively
     *
     * @return array<int, array{name: string, copyright: string|null, objectType: string, compilerVersionCode: int}>
     */
    private function extractMetadata(Node $node, int $versionCode): array
    {
        $metadata = [];

        // Check if this node is a named object (has TEXT child nodes)
        if ($node->hasChildren()) {
            $firstChild = $node->getChild(0);
            if ($firstChild instanceof \App\Services\FileInfo\Extractors\Pak\Node && $firstChild->isType(Node::OBJ_TEXT)) {
                // This is a named object
                $pakMetadata = PakMetadata::fromNode($node, $versionCode);
                $metadata[] = $pakMetadata->toArray();
            }
        }

        // Recursively search child nodes
        foreach ($node->getChildren() as $child) {
            $childMetadata = $this->extractMetadata($child, $versionCode);
            $metadata = array_merge($metadata, $childMetadata);
        }

        return $metadata;
    }
}
