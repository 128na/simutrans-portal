<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak;

use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;

trait MakesTestNodes
{
    /**
     * @param  array<string>  $children  encodeNode() で作った子ノードの生バイナリ列
     */
    private function makeNode(string $type, string $data, array $children = []): Node
    {
        return Node::parse(new BinaryReader($this->encodeNode($type, $data, $children)));
    }

    /**
     * @param  array<string>  $children  encodeNode() で作った子ノードの生バイナリ列
     */
    private function makeVersionedNode(string $type, int $version, string $payload, array $children = []): Node
    {
        return $this->makeNode($type, pack('v', 0x8000 | $version).$payload, $children);
    }

    /**
     * ノード1つをバイナリにエンコードする (type[4] + children[2] + size[2] + data + 子ノード群)。
     *
     * @param  array<string>  $children  encodeNode() で作った子ノードのバイナリ列
     */
    private function encodeNode(string $type, string $data, array $children = []): string
    {
        $type = str_pad($type, 4, "\x00");

        return $type.pack('v', count($children)).pack('v', strlen($data)).$data.implode('', $children);
    }
}
