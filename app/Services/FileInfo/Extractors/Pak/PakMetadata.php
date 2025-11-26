<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Pak metadata information
 */
final readonly class PakMetadata
{
    private function __construct(
        public string $name,
        public ?string $copyright,
        public string $objectType,
        public int $compilerVersionCode,
    ) {}

    /**
     * @return array{name: string, copyright: string|null, objectType: string, compilerVersionCode: int}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'copyright' => $this->copyright,
            'objectType' => $this->objectType,
            'compilerVersionCode' => $this->compilerVersionCode,
        ];
    }

    public static function fromNode(Node $node, int $versionCode): self
    {
        // Child node 0 is name (TEXT node)
        $nameNode = $node->getChild(0);
        $name = '';
        if ($nameNode instanceof \App\Services\FileInfo\Extractors\Pak\Node && $nameNode->isType(Node::OBJ_TEXT)) {
            $name = self::extractTextFromNode($nameNode);
        }

        // Child node 1 is copyright (TEXT node)
        $copyrightNode = $node->getChild(1);
        $copyright = null;
        if ($copyrightNode instanceof \App\Services\FileInfo\Extractors\Pak\Node && $copyrightNode->isType(Node::OBJ_TEXT)) {
            $copyright = self::extractTextFromNode($copyrightNode);
            if ($copyright === '') {
                $copyright = null;
            }
        }

        // Determine object type from parent node
        $objectType = ObjectTypeConverter::toString($node->type);

        return new self($name, $copyright, $objectType, $versionCode);
    }

    private static function extractTextFromNode(Node $node): string
    {
        // TEXT node data is a null-terminated string
        $nullPos = strpos($node->data, "\0");
        if ($nullPos === false) {
            return $node->data;
        }

        return substr($node->data, 0, $nullPos);
    }
}
