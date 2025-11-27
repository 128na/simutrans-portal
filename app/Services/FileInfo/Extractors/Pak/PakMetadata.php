<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

use App\Services\FileInfo\Extractors\Pak\TypeParsers\BridgeParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\BuildingParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\GoodParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\SignParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\TunnelParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\TypeParserInterface;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\VehicleParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\WayObjectParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\WayParser;

/**
 * Pak metadata information
 */
final readonly class PakMetadata
{
    /**
     * @param  array<string, mixed>  $typeSpecificData  オブジェクトタイプ固有のデータ（vehicle, way, building等）
     */
    private function __construct(
        public string $name,
        public ?string $copyright,
        public string $objectType,
        public int $compilerVersionCode,
        public array $typeSpecificData = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'name' => $this->name,
            'copyright' => $this->copyright,
            'objectType' => $this->objectType,
            'compilerVersionCode' => $this->compilerVersionCode,
        ];

        // Add type-specific data with appropriate keys for backward compatibility
        if ($this->typeSpecificData !== []) {
            // Use objectType to determine the key name (e.g., 'vehicleData', 'wayData')
            $dataKey = $this->objectType . 'Data';
            $result[$dataKey] = $this->typeSpecificData;
        }

        return $result;
    }

    public static function fromNode(Node $node, int $versionCode): self
    {
        // Child node 0 is name (TEXT node)
        $nameNode = $node->getChild(0);
        $name = '';
        if ($nameNode instanceof \App\Services\FileInfo\Extractors\Pak\Node && $nameNode->isType(Node::OBJ_TEXT)) {
            $name = TextNodeExtractor::extract($nameNode);
        }

        // Child node 1 is copyright (TEXT node)
        $copyrightNode = $node->getChild(1);
        $copyright = null;
        if ($copyrightNode instanceof \App\Services\FileInfo\Extractors\Pak\Node && $copyrightNode->isType(Node::OBJ_TEXT)) {
            $copyright = TextNodeExtractor::extract($copyrightNode);
            if ($copyright === '') {
                $copyright = null;
            }
        }

        // Determine object type from parent node
        $objectType = ObjectTypeConverter::toString($node->type);

        // Parse type-specific data using appropriate parser
        $typeSpecificData = [];
        foreach (self::getTypeParsers() as $typeParser) {
            if ($typeParser->canParse($node)) {
                $typeSpecificData = $typeParser->parse($node) ?? [];
                break;
            }
        }

        return new self($name, $copyright, $objectType, $versionCode, $typeSpecificData);
    }

    /**
     * Get type parsers
     *
     * @return array<TypeParserInterface>
     */
    private static function getTypeParsers(): array
    {
        static $parsers = null;

        if ($parsers === null) {
            $parsers = [
                new VehicleParser,
                new WayParser,
                new WayObjectParser,
                new BridgeParser,
                new TunnelParser,
                new SignParser,
                new BuildingParser,
            ];
        }

        return $parsers;
    }
}
