<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

use App\Services\FileInfo\Extractors\Pak\TypeParsers\BridgeParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\BuildingParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\CitycarParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\CrossingParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\FactoryParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\GoodParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\GroundobjParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\GroundParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\PedestrianParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\SignParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\SkinParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\SoundParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\TreeParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\TunnelParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\TypeParserInterface;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\VehicleParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\WayObjectParser;
use App\Services\FileInfo\Extractors\Pak\TypeParsers\WayParser;

/**
 * Pak metadata information
 */
class PakMetadata
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
            $dataKey = $this->objectType.'Data';
            $result[$dataKey] = $this->typeSpecificData;
        }

        return $result;
    }

    public static function fromNode(Node $node, int $versionCode): self
    {
        // Determine object type first
        $objectType = ObjectTypeConverter::toString($node->type);

        // Child node 0 is name (TEXT node)
        $nameNode = $node->getChild(0);
        $name = '';
        if ($nameNode instanceof \App\Services\FileInfo\Extractors\Pak\Node && $nameNode->isType(Node::OBJ_TEXT)) {
            $name = TextNodeExtractor::extract($nameNode);
        } elseif ($objectType === 'factory' && $nameNode instanceof \App\Services\FileInfo\Extractors\Pak\Node) {
            // Special case: Factory (FACT) node has BUIL node as first child
            // Extract name from BUIL node's first child (TEXT node)
            $buildingTextNode = $nameNode->getChild(0);
            if ($buildingTextNode instanceof \App\Services\FileInfo\Extractors\Pak\Node && $buildingTextNode->isType(Node::OBJ_TEXT)) {
                $name = TextNodeExtractor::extract($buildingTextNode);
            }
        }

        // Child node 1 is copyright (TEXT node)
        $copyrightNode = $node->getChild(1);
        $copyright = null;
        if ($copyrightNode instanceof \App\Services\FileInfo\Extractors\Pak\Node && $copyrightNode->isType(Node::OBJ_TEXT)) {
            $copyright = TextNodeExtractor::extract($copyrightNode);
            if ($copyright === '') {
                $copyright = null;
            }
        } elseif ($objectType === 'factory' && $nameNode instanceof \App\Services\FileInfo\Extractors\Pak\Node) {
            // Special case: Factory (FACT) node - get copyright from BUIL node's second child
            $buildingCopyrightNode = $nameNode->getChild(1);
            if ($buildingCopyrightNode instanceof \App\Services\FileInfo\Extractors\Pak\Node && $buildingCopyrightNode->isType(Node::OBJ_TEXT)) {
                $copyright = TextNodeExtractor::extract($buildingCopyrightNode);
                if ($copyright === '') {
                    $copyright = null;
                }
            }
        }

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
                new CrossingParser,
                new CitycarParser,
                new FactoryParser,
                new GoodParser,
                new BuildingParser,
                new PedestrianParser,
                new TreeParser,
                new GroundobjParser,
                new GroundParser,
                new SoundParser,
                new SkinParser,
            ];
        }

        return $parsers;
    }
}
