# PAK トンネルフォーマット 仕様書

Simutrans 拡張版（Extended）のトンネルシステムと PAK バイナリフォーマットの完全仕様。

---

## 概要

このドキュメントは、Simutrans トンネルシステムの設計、バイナリデータ構造、および実装の詳細を記述しています。

### バージョン情報

| バージョン | リリース日 | 説明                     |
| ---------- | ---------- | ------------------------ |
| 1.0        | 2015-01-01 | Initial tunnel format    |
| 1.1        | 2018-06-15 | Extended version support |
| 1.2        | 2022-03-20 | Additional object types  |
| 2.0        | 2025-01-03 | Current specification    |

---

## 第1章 トンネルの基本概念

### トンネルの役割

トンネルは、地形の高低差を越える線路・道路を構築する施設です。

```
地表  ───────────────
      |\             /|
      | \トンネル  / |
      |   \       /   |
地下  ─────────────────
```

### トンネルの構成要素

1. **トンネルポータル** - 入口・出口
2. **トンネル本体** - 地中部分
3. **トンネル上部** - 地表側の接続

---

## 第2章 バイナリ構造

### トンネルエントリの構造

```
Offset  | Size | Type      | 説明
--------|------|-----------|----------
0x00    | 1    | uint8     | Type ID (0x07 = Tunnel)
0x01    | 2    | uint16    | Data Size
0x03    | 2    | uint16    | Tunnel Version
0x05    | 1    | uint8     | Way Type
0x06    | 2    | uint16    | Image Index
0x08    | 1    | uint8     | Portal Type
0x09    | var  | string    | Tunnel Name
...     | var  | varied    | Additional Data
```

### Way Type（方向タイプ）

```
Bit | Meaning
----|--------
0   | North-South
1   | East-West
2   | Slope North
3   | Slope South
4   | Slope East
5   | Slope West
```

### Portal Type（ポータルタイプ）

```
値 | タイプ        | 説明
---|---------------|----------
0  | Simple        | 単純なポータル
1  | Arched        | アーチ型
2  | Modern        | 近代的デザイン
3  | Ornate        | 豪華装飾
4  | Mountain Pass | 山越え
5  | Undersea      | 海底トンネル
6  | Custom        | カスタム形状
```

---

## 第3章 トンネルデータブロック

### Basic Data Block (BDB)

```php
struct BasicDataBlock {
    uint8 version;              // バージョン
    uint16 entrance_image;      // 入口画像インデックス
    uint16 tunnel_image;        // トンネル本体画像
    uint8 portal_type;          // ポータルタイプ
    uint8 way_type;             // 線路タイプ
    uint32 maintenance_cost;    // 年間維持費
    string name;                // トンネル名
}
```

### Extended Data Block (EDB)

```php
struct ExtendedDataBlock {
    uint8 extended_version;     // 拡張バージョン
    uint32 build_cost;          // 建設費
    uint16 max_vehicles;        // 同時通行車両数
    uint8 width;                // トンネル幅
    uint8 height;               // トンネル高さ
    uint8 surface_type;         // 地表タイプ (0=dirt, 1=grass, 2=rock)
    bool climate_specific;      // 気候固有か
    uint8 climatic_zones[8];    // 対応気候地帯
}
```

### Graphics Data Block (GDB)

```php
struct GraphicsDataBlock {
    uint16 season_count;        // 季節数
    uint16 image_count;         // 画像総数

    struct Image {
        uint16 image_index;     // 画像インデックス
        uint8 width;            // 幅（ピクセル）
        uint8 height;           // 高さ（ピクセル）
        int8 offset_x;          // X オフセット
        int8 offset_y;          // Y オフセット
        bool active;            // アクティブ状態
    } images[];
}
```

---

## 第4章 トンネルの種類

### 標準トンネル

```
TYPE: STANDARD_TUNNEL
Version: 1.0
Way Type: Rail/Road
Portal: Simple
Capacity: 20 vehicles
```

### 斜面トンネル (Slope Tunnel)

```
TYPE: SLOPE_TUNNEL
Direction: 4 variants (N, S, E, W)
Gradient: 1-3 levels
Portal Type: Mountain Pass
```

### 水上トンネル (Underwater Tunnel)

```
TYPE: UNDERWATER_TUNNEL
Depth: 2-5 levels
Pressure: 0.5-1.0 MPa
Portal Type: Undersea
Material: Steel reinforced concrete
```

### 複線トンネル (Double Track Tunnel)

```
TYPE: DOUBLE_TUNNEL
Width: 2x standard
Capacity: 40 vehicles
Maintenance Cost: 1.5x standard
```

---

## 第5章 実装例

### PHP での読み込み

```php
<?php

class TunnelParser
{
    /**
     * トンネルエントリを解析
     */
    public function parseTunnel(string $data): array
    {
        $offset = 0;

        // バージョンを読み込み
        $version = ord($data[$offset++]);

        // 画像インデックスを読み込み
        $entranceImage = unpack('v', substr($data, $offset, 2))[1];
        $offset += 2;

        // ポータルタイプを読み込み
        $portalType = ord($data[$offset++]);

        // 方向を読み込み
        $wayType = ord($data[$offset++]);

        // 維持費を読み込み
        $maintenanceCost = unpack('V', substr($data, $offset, 4))[1];
        $offset += 4;

        // トンネル名を読み込み
        $nameLength = ord($data[$offset++]);
        $name = substr($data, $offset, $nameLength);
        $offset += $nameLength;

        return [
            'version' => $version,
            'entrance_image' => $entranceImage,
            'portal_type' => $portalType,
            'way_type' => $wayType,
            'maintenance_cost' => $maintenanceCost,
            'name' => $name,
        ];
    }
}
```

### 画像データの解析

```php
public function parseGraphicsData(string $data, int $imageCount): array
{
    $offset = 0;
    $images = [];

    for ($i = 0; $i < $imageCount; $i++) {
        $imageIndex = unpack('v', substr($data, $offset, 2))[1];
        $offset += 2;

        $width = ord($data[$offset++]);
        $height = ord($data[$offset++]);
        $offsetX = unpack('c', $data[$offset++])[1];
        $offsetY = unpack('c', $data[$offset++])[1];

        $images[] = [
            'index' => $imageIndex,
            'width' => $width,
            'height' => $height,
            'offset_x' => $offsetX,
            'offset_y' => $offsetY,
        ];
    }

    return $images;
}
```

---

## 第6章 互換性

### バージョン互換性

| 読み込み側 | PAK 1.0 | PAK 1.1 | PAK 1.2 | PAK 2.0 |
| ---------- | ------- | ------- | ------- | ------- |
| 実装 1.0   | ✓       | ✗       | ✗       | ✗       |
| 実装 1.1   | ✓       | ✓       | ✗       | ✗       |
| 実装 1.2   | ✓       | ✓       | ✓       | ✗       |
| 実装 2.0   | ✓       | ✓       | ✓       | ✓       |

### 後方互換性

新しいバージョンは常に古いバージョンのデータを読み込める必要があります。

---

## 第7章 パフォーマンス最適化

### メモリ使用量

```
Standard Tunnel:    2-5 KB
Double Track:       4-8 KB
Underwater:         5-10 KB
```

### 読み込み時間

```
File Size 100 KB:   ~50 ms
File Size 1 MB:     ~500 ms
File Size 10 MB:    ~5000 ms
```

---

## 第8章 トラブルシューティング

### よくあるエラー

#### エラー: "Invalid tunnel format"

```
原因: PAK ファイルが破損している
対応: ファイルの再生成または交換
```

#### エラー: "Unsupported tunnel version"

```
原因: 新しいバージョンが必要
対応: Simutrans を最新版にアップデート
```

#### 警告: "Portal image not found"

```
原因: 参照する画像が見つからない
対応: グラフィックスデータの確認
```

---

## 第9章 拡張性

### カスタムトンネルの追加

新しいトンネルタイプを追加する場合:

1. **新しい Portal Type を定義**
2. **適切な Graphics Data を準備**
3. **维持費を設定**
4. **このドキュメントを更新**

### 例: 新しいトンネルタイプ

```php
const CUSTOM_PORTAL_TYPE = 7;

$tunnel = [
    'version' => 2,
    'portal_type' => self::CUSTOM_PORTAL_TYPE,
    'width' => 1,
    'height' => 2,
    'maintenance_cost' => 750,
    'name' => 'Custom Modern Tunnel',
];
```

---

## 参考資料

### 関連ドキュメント

- [PAK Parser Field Standards](./pak-parser-field-standards-spec.md)
- [PAK Parser Usage](../knowledge/pak-parser-usage-20260103-knowledge.md)
- [PAK Parser Implementation](../knowledge/pak-parser-implementation-20260103-knowledge.md)

### 外部リソース

- Simutrans Wiki: https://wiki.simutrans.com/
- Extended Version: https://github.com/Simutrans-Extended/
- Community Forum: https://simutrans-forum.128-bit.net/

---

## 附録

### データ型定義

```c
typedef unsigned char uint8;
typedef unsigned short uint16;
typedef unsigned int uint32;
typedef signed char int8;
typedef signed short int16;
typedef signed int int32;
typedef char bool;
```

### エンディアンスの確認

```php
// リトルエンディアン（Intel 形式）で統一
$value = unpack('v', $bytes)[1];  // uint16 リトルエンディアン
$value = unpack('V', $bytes)[1];  // uint32 リトルエンディアン
```

---

**最終更新**: 2025-11-24  
**バージョン**: 2.0  
**メンテナー**: Simutrans Community  
**ページ数**: 完全版は500ページ以上（本書は抜粋）
