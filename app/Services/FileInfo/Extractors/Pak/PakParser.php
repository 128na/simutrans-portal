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
     * @return array<int, array<string, mixed>>
     */
    public function parse(string $binary): array
    {
        $reader = new BinaryReader($binary);

        // Parse header
        $header = PakHeader::parse($reader);

        // Parse root node
        $root = Node::parse($reader);

        // Extract metadata from nodes
        return $this->extractMetadata($root, $header->compilerVersionCode);
    }

    /**
     * Extract metadata from node tree recursively
     *
     * @return array<int, array<string, mixed>>
     */
    private function extractMetadata(Node $node, int $versionCode): array
    {
        $metadata = [];

        // Skip CURS (cursor) nodes - they don't need metadata extraction
        // (ref: simutrans/descriptor/reader/obj_reader.cc - register_nodes<2 || node.type!=obj_cursor)
        if ($node->isType(Node::OBJ_CURSOR)) {
            return $metadata;
        }

        // Check if this node is a named object (has TEXT child nodes)
        if ($node->hasChildren()) {
            $firstChild = $node->getChild(0);
            if ($firstChild instanceof \App\Services\FileInfo\Extractors\Pak\Node && $firstChild->isType(Node::OBJ_TEXT)) {
                // This is a named object
                $pakMetadata = PakMetadata::fromNode($node, $versionCode);
                $metadata[] = $pakMetadata->toArray();
            } elseif ($firstChild instanceof \App\Services\FileInfo\Extractors\Pak\Node && ObjectTypeConverter::toString($firstChild->type) === 'building') {
                // Special case: FACT node has BUIL as first child
                // Check if this is a factory (FACT node containing BUIL node)
                $objectType = ObjectTypeConverter::toString($node->type);
                if ($objectType === 'factory' && $firstChild->hasChildren()) {
                    $buildingFirstChild = $firstChild->getChild(0);
                    if ($buildingFirstChild instanceof \App\Services\FileInfo\Extractors\Pak\Node && $buildingFirstChild->isType(Node::OBJ_TEXT)) {
                        // This is a factory node - process it
                        $pakMetadata = PakMetadata::fromNode($node, $versionCode);
                        $metadata[] = $pakMetadata->toArray();
                    }
                }
            }
        }

        // Recursively search child nodes (skip CURS nodes)
        foreach ($node->getChildren() as $child) {
            // Skip CURS nodes in recursion as well
            if ($child->isType(Node::OBJ_CURSOR)) {
                continue;
            }

            $childMetadata = $this->extractMetadata($child, $versionCode);
            $metadata = array_merge($metadata, $childMetadata);
        }

        return $metadata;
    }
}
