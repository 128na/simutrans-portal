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
            Node::OBJ_WAY => 'way',
            Node::OBJ_TREE => 'tree',
            Node::OBJ_GOOD => 'good',
            default => sprintf('unknown_%s', $type),
        };
    }
}
