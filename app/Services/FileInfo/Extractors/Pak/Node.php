<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

use App\Exceptions\InvalidPakFileException;

/**
 * Pak file node structure
 */
class Node
{
    public const string OBJ_TEXT = 'TEXT';

    public const string OBJ_ROOT = 'ROOT';

    public const string OBJ_VEHICLE = 'VHCL';

    public const string OBJ_BUILDING = 'BUIL';

    public const string OBJ_BRIDGE = 'BRDG';

    public const string OBJ_TUNNEL = 'TUNL';

    public const string OBJ_WAY = "WAY\0";

    public const string OBJ_WAYOBJ = 'WYOB';

    public const string OBJ_ROADSIGN = 'SIGN';

    public const string OBJ_CROSSING = 'CRSS';

    public const string OBJ_TREE = 'TREE';

    public const string OBJ_GROUNDOBJ = 'GOBJ';

    public const string OBJ_GROUND = 'GRND';

    public const string OBJ_GOOD = 'GOOD';

    public const string OBJ_FACTORY = 'FACT';

    public const string OBJ_FACTORY_SUPPLIER = 'FSUP';

    public const string OBJ_FACTORY_PRODUCT = 'FPRO';

    public const string OBJ_CITYCAR = 'CCAR';

    public const string OBJ_PEDESTRIAN = 'PASS';

    public const string OBJ_SOUND = 'SOND';

    public const string OBJ_SKIN = 'SKIN';

    public const string OBJ_CURSOR = 'CURS';

    public const string OBJ_XREF = 'XREF';

    private const int LARGE_RECORD_SIZE = 0xFFFF;

    private const int MAX_DEPTH = 100;

    /**
     * @param  array<int, Node>  $childNodes
     */
    private function __construct(
        public string $type,
        public int $children,
        public int $size,
        public string $data,
        public array $childNodes = [],
    ) {}

    public static function parse(BinaryReader $reader, int $depth = 0): self
    {
        if ($depth > self::MAX_DEPTH) {
            throw InvalidPakFileException::corruptedNode('Maximum nesting depth exceeded');
        }

        // Read node header
        $type = $reader->readString(4); // Read 4-char ASCII type
        $children = $reader->readUint16LE();
        $size = $reader->readUint16LE();

        // Check for extended size
        if ($size === self::LARGE_RECORD_SIZE) {
            $size = $reader->readUint32LE();
        }

        // Read node data
        $data = $size > 0 ? $reader->readString($size) : '';

        // Parse child nodes recursively
        $childNodes = [];
        for ($i = 0; $i < $children; $i++) {
            $childNodes[] = self::parse($reader, $depth + 1);
        }

        return new self($type, $children, $size, $data, $childNodes);
    }

    public function isType(string $type): bool
    {
        return $this->type === $type;
    }

    public function hasChildren(): bool
    {
        return $this->children > 0;
    }

    /**
     * @return array<int, Node>
     */
    public function getChildren(): array
    {
        return $this->childNodes;
    }

    public function getChild(int $index): ?self
    {
        return $this->childNodes[$index] ?? null;
    }
}
