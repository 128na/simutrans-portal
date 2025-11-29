<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Convert object type ID to human-readable string
 */
final class ObjectTypeConverter
{
    public static function toString(string $type): string
    {
        return match ($type) {
            Node::OBJ_VEHICLE => 'vehicle',
            Node::OBJ_BUILDING => 'building',
            Node::OBJ_BRIDGE => 'bridge',
            Node::OBJ_TUNNEL => 'tunnel',
            Node::OBJ_WAY => 'way',
            Node::OBJ_WAYOBJ => 'wayobj',
            Node::OBJ_ROADSIGN => 'roadsign',
            Node::OBJ_CROSSING => 'crossing',
            Node::OBJ_TREE => 'tree',
            Node::OBJ_GROUNDOBJ => 'groundobj',
            Node::OBJ_GROUND => 'ground',
            Node::OBJ_GOOD => 'good',
            Node::OBJ_FACTORY => 'factory',
            Node::OBJ_FACTORY_SUPPLIER => 'fsup',
            Node::OBJ_FACTORY_PRODUCT => 'fpro',
            Node::OBJ_XREF => 'xref',
            Node::OBJ_CITYCAR, 'CCAR' => 'citycar', // CCAR is used by makeobj 60.8+
            Node::OBJ_PEDESTRIAN => 'pedestrian',
            Node::OBJ_SOUND => 'sound',
            Node::OBJ_SKIN => 'skin',
            default => sprintf('unknown_%s', $type),
        };
    }
}
