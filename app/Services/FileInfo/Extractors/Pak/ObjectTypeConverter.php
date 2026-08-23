<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

use App\Enums\PakObjectType;

/**
 * Convert object type ID to human-readable string
 */
class ObjectTypeConverter
{
    public static function toEnum(string $type): ?PakObjectType
    {
        return match ($type) {
            Node::OBJ_VEHICLE => PakObjectType::Vehicle,
            Node::OBJ_BUILDING => PakObjectType::Building,
            Node::OBJ_BRIDGE => PakObjectType::Bridge,
            Node::OBJ_TUNNEL => PakObjectType::Tunnel,
            Node::OBJ_WAY => PakObjectType::Way,
            Node::OBJ_WAYOBJ => PakObjectType::WayObject,
            Node::OBJ_ROADSIGN => PakObjectType::RoadSign,
            Node::OBJ_CROSSING => PakObjectType::Crossing,
            Node::OBJ_TREE => PakObjectType::Tree,
            Node::OBJ_GROUNDOBJ => PakObjectType::GroundObject,
            Node::OBJ_GROUND => PakObjectType::Ground,
            Node::OBJ_GOOD => PakObjectType::Good,
            Node::OBJ_FACTORY => PakObjectType::Factory,
            Node::OBJ_FACTORY_SUPPLIER => PakObjectType::FactorySupplier,
            Node::OBJ_FACTORY_PRODUCT => PakObjectType::FactoryProduct,
            Node::OBJ_FACTORY_FIELD_GROUP => PakObjectType::FactoryFieldGroup,
            Node::OBJ_FACTORY_FIELD_CLASS => PakObjectType::FactoryFieldClass,
            Node::OBJ_FACTORY_SMOKE => PakObjectType::FactorySmoke,
            Node::OBJ_XREF => PakObjectType::Xref,
            Node::OBJ_CITYCAR => PakObjectType::Citycar, // Node::OBJ_CITYCAR is itself 'CCAR' (makeobj 60.8+)
            Node::OBJ_PEDESTRIAN => PakObjectType::Pedestrian,
            Node::OBJ_SOUND => PakObjectType::Sound,
            Node::OBJ_MENU => PakObjectType::Menu,
            Node::OBJ_CURSOR => PakObjectType::Cursor,
            Node::OBJ_SYMBOL => PakObjectType::Symbol,
            Node::OBJ_FIELD => PakObjectType::Field,
            Node::OBJ_SMOKE => PakObjectType::Smoke,
            Node::OBJ_MISCIMAGES => PakObjectType::MiscImages,
            Node::OBJ_TILE => PakObjectType::Tile,
            Node::OBJ_IMAGE => PakObjectType::Image,
            Node::OBJ_IMAGE_LIST => PakObjectType::ImageList,
            Node::OBJ_IMAGE_LIST_2D => PakObjectType::ImageList2D,
            default => null,
        };
    }

    public static function toString(string $type): string
    {
        return self::toEnum($type)->value ?? sprintf('unknown_%s', $type);
    }
}
