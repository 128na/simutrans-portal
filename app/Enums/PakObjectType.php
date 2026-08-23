<?php

declare(strict_types=1);

namespace App\Enums;

use App\Services\FileInfo\Extractors\Pak\ObjectTypeConverter;

/**
 * Simutrans .pak ノードのオブジェクト種別。
 *
 * 値は既存の {@see ObjectTypeConverter::toString()}
 * が返してきた文字列と完全に一致させる（DB永続化・フロントエンドの ObjectType 型契約を
 * 変えないため）。
 *
 * - Vehicle〜MiscImages: フロントの ObjectType 型にも現れるトップレベルのオブジェクト種別
 * - FactorySupplier〜Xref: FACT（工場）ノードの子ノード判別専用（パース内部のみで使用）
 * - Tile〜ImageList2D: 画像リソース系ノードの子ノード判別専用（パース内部のみで使用）
 */
enum PakObjectType: string
{
    case Vehicle = 'vehicle';
    case Building = 'building';
    case Bridge = 'bridge';
    case Tunnel = 'tunnel';
    case Way = 'way';
    case WayObject = 'wayobj';
    case RoadSign = 'roadsign';
    case Crossing = 'crossing';
    case Tree = 'tree';
    case GroundObject = 'groundobj';
    case Ground = 'ground';
    case Good = 'good';
    case Factory = 'factory';
    case Citycar = 'citycar';
    case Pedestrian = 'pedestrian';
    case Sound = 'sound';
    case Menu = 'menu';
    case Cursor = 'cursor';
    case Symbol = 'symbol';
    case Field = 'field';
    case Smoke = 'smoke';
    case MiscImages = 'miscimages';

    case FactorySupplier = 'fsup';
    case FactoryProduct = 'fpro';
    case FactoryFieldGroup = 'ffield';
    case FactoryFieldClass = 'ffldclass';
    case FactorySmoke = 'fsmoke';
    case Xref = 'xref';

    case Tile = 'tile';
    case Image = 'image';
    case ImageList = 'imagelist';
    case ImageList2D = 'imagelist2d';
}
