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
            'factory' => [Node::OBJ_FACTORY, PakObjectType::Factory],
            'factory supplier' => [Node::OBJ_FACTORY_SUPPLIER, PakObjectType::FactorySupplier],
            'factory product' => [Node::OBJ_FACTORY_PRODUCT, PakObjectType::FactoryProduct],
            'factory field group' => [Node::OBJ_FACTORY_FIELD_GROUP, PakObjectType::FactoryFieldGroup],
            'factory field class' => [Node::OBJ_FACTORY_FIELD_CLASS, PakObjectType::FactoryFieldClass],
            'factory smoke' => [Node::OBJ_FACTORY_SMOKE, PakObjectType::FactorySmoke],
            'xref' => [Node::OBJ_XREF, PakObjectType::Xref],
            'citycar' => [Node::OBJ_CITYCAR, PakObjectType::Citycar],
            'citycar (CCAR alias)' => ['CCAR', PakObjectType::Citycar],
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

    public function test_to_enum_returns_null_for_unknown_node_type(): void
    {
        $this->assertNull(ObjectTypeConverter::toEnum('ZZZZ'));
    }

    public function test_to_string_falls_back_to_unknown_prefix_for_unknown_node_type(): void
    {
        $this->assertSame('unknown_ZZZZ', ObjectTypeConverter::toString('ZZZZ'));
    }
}
