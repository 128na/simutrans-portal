<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak;

use App\Enums\PakObjectType;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\ObjectTypeConverter;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

class ObjectTypeConverterTest extends TestCase
{
    /**
     * @return array<string, array{string, PakObjectType}>
     */
    public static function knownTypeProvider(): array
    {
        return [
            'vehicle' => [Node::OBJ_VEHICLE, PakObjectType::Vehicle],
            'building' => [Node::OBJ_BUILDING, PakObjectType::Building],
            'bridge' => [Node::OBJ_BRIDGE, PakObjectType::Bridge],
            'tunnel' => [Node::OBJ_TUNNEL, PakObjectType::Tunnel],
            'way' => [Node::OBJ_WAY, PakObjectType::Way],
            'wayobj' => [Node::OBJ_WAYOBJ, PakObjectType::WayObject],
            'roadsign' => [Node::OBJ_ROADSIGN, PakObjectType::RoadSign],
            'crossing' => [Node::OBJ_CROSSING, PakObjectType::Crossing],
            'tree' => [Node::OBJ_TREE, PakObjectType::Tree],
            'groundobj' => [Node::OBJ_GROUNDOBJ, PakObjectType::GroundObject],
            'ground' => [Node::OBJ_GROUND, PakObjectType::Ground],
            'good' => [Node::OBJ_GOOD, PakObjectType::Good],
            'factory' => [Node::OBJ_FACTORY, PakObjectType::Factory],
            'factory supplier' => [Node::OBJ_FACTORY_SUPPLIER, PakObjectType::FactorySupplier],
            'factory product' => [Node::OBJ_FACTORY_PRODUCT, PakObjectType::FactoryProduct],
            'factory field group' => [Node::OBJ_FACTORY_FIELD_GROUP, PakObjectType::FactoryFieldGroup],
            'factory field class' => [Node::OBJ_FACTORY_FIELD_CLASS, PakObjectType::FactoryFieldClass],
            'factory smoke' => [Node::OBJ_FACTORY_SMOKE, PakObjectType::FactorySmoke],
            'xref' => [Node::OBJ_XREF, PakObjectType::Xref],
            'citycar' => [Node::OBJ_CITYCAR, PakObjectType::Citycar],
            'pedestrian' => [Node::OBJ_PEDESTRIAN, PakObjectType::Pedestrian],
            'sound' => [Node::OBJ_SOUND, PakObjectType::Sound],
            'menu' => [Node::OBJ_MENU, PakObjectType::Menu],
            'cursor' => [Node::OBJ_CURSOR, PakObjectType::Cursor],
            'symbol' => [Node::OBJ_SYMBOL, PakObjectType::Symbol],
            'field' => [Node::OBJ_FIELD, PakObjectType::Field],
            'smoke' => [Node::OBJ_SMOKE, PakObjectType::Smoke],
            'miscimages' => [Node::OBJ_MISCIMAGES, PakObjectType::MiscImages],
            'tile' => [Node::OBJ_TILE, PakObjectType::Tile],
            'image' => [Node::OBJ_IMAGE, PakObjectType::Image],
            'imagelist' => [Node::OBJ_IMAGE_LIST, PakObjectType::ImageList],
            'imagelist2d' => [Node::OBJ_IMAGE_LIST_2D, PakObjectType::ImageList2D],
        ];
    }

    #[DataProvider('knownTypeProvider')]
    public function test_to_enum_maps_known_node_type_to_enum_case(string $rawType, PakObjectType $expected): void
    {
        $this->assertSame($expected, ObjectTypeConverter::toEnum($rawType));
    }

    #[DataProvider('knownTypeProvider')]
    public function test_to_string_matches_enum_value_for_known_node_type(string $rawType, PakObjectType $expected): void
    {
        $this->assertSame($expected->value, ObjectTypeConverter::toString($rawType));
    }

    public function test_all_enum_cases_are_covered_by_the_provider(): void
    {
        $coveredCases = array_map(
            static fn (array $case): PakObjectType => $case[1],
            self::knownTypeProvider()
        );

        $this->assertEqualsCanonicalizing(PakObjectType::cases(), array_values(array_unique($coveredCases, SORT_REGULAR)));
    }

    public function test_to_enum_returns_null_for_unknown_node_type(): void
    {
        $this->assertNull(ObjectTypeConverter::toEnum('ZZZZ'));
    }

    public function test_to_string_falls_back_to_unknown_prefix_for_unknown_node_type(): void
    {
        $this->assertSame('unknown_ZZZZ', ObjectTypeConverter::toString('ZZZZ'));
    }
}
