# PAK パーサー フィールド標準仕様

Simutrans PAK ファイルのフィールド命名規則と標準的なデータ構造定義。

---

## 概要

このドキュメントは、Simutrans の Squirrel API と PAK バイナリフォーマット間でのフィールド命名規則を統一し、メタデータ抽出の一貫性を確保するための標準仕様です。

---

## フィールド命名規則

### 基本原則

- **スネークケース**: `object_id`, `vehicle_name`, `cargo_capacity`
- **英語**: 多言語対応を想定し英語で統一
- **動詞は避ける**: `is_active` → `active`、`can_load` → `loadable`

### プレフィックス

| プレフィックス | 用途           | 例                             |
| -------------- | -------------- | ------------------------------ |
| `{name}_`      | 名前・テキスト | `vehicle_name`, `author_name`  |
| `max_{name}`   | 最大値         | `max_speed`, `max_capacity`    |
| `min_{name}`   | 最小値         | `min_weight`, `min_passengers` |
| `has_{name}`   | 所有・保有     | `has_mail_car`, `has_engine`   |

### サフィックス

| サフィックス | 用途   | 例                             |
| ------------ | ------ | ------------------------------ |
| `_{count}`   | 数量   | `wagon_count`, `vehicle_count` |
| `_{type}`    | タイプ | `cargo_type`, `vehicle_type`   |
| `_{id}`      | ID     | `object_id`, `vehicle_id`      |

---

## 標準フィールド定義

### Vehicle（乗り物）

```
基本属性:
- object_id: string              # "DB-101"
- name: string                   # "Deutsche Bahn 101"
- name_en: string               # 英語名
- name_ja: string               # 日本語名
- version: uint16               # バージョン (例: 0x0101 = 1.1)

性能:
- max_speed: uint16             # 最大速度 (km/h)
- weight: uint16                # 重量 (トン)
- power: uint16                 # 馬力 (kW)
- tractive_effort: uint16       # 牽引力

容量:
- passenger_capacity: uint16    # 旅客定員
- cargo_capacity: uint16        # 貨物積載量
- mail_capacity: uint16         # 郵便積載量

タイプ:
- vehicle_type: uint8           # 0=rail, 1=road, 2=water, 3=air
- cargo_type: uint8             # 旅客/貨物/郵便等

年号:
- year_from: uint16             # 就役年 (例: 1985)
- year_to: uint16               # 廃止年 (例: 2005)

フラグ:
- engine: bool                  # 機関車か
- powered: bool                 # 動力あり
- passenger: bool               # 旅客車か
- freight: bool                 # 貨物車か
```

### Building（建物）

```
基本属性:
- object_id: string             # "STATION_1"
- name: string                  # "Simple Station"
- author: string                # "Simutrans Community"
- version: uint16

物理属性:
- width: uint8                  # 幅（セル数）
- height: uint8                 # 高さ（セル数）
- depth: uint8                  # 奥行き（セル数）

容量:
- capacity: uint16              # 収容人数
- mail_capacity: uint16         # 郵便容量

タイプ:
- building_type: uint8          # 0=station, 1=factory, 2=city_building
- cargo_type: uint8             # 取扱貨物タイプ

経済:
- building_cost: uint32         # 建設費
- maintenance_cost: uint16      # 年間維持費
- monthly_cost: uint32          # 月間運営費

フラグ:
- removal_cost: uint32          # 取壊費用
```

### Scenery（風景・装飾）

```
基本属性:
- object_id: string             # "TREE_OAK"
- name: string
- author: string

物理属性:
- width: uint8
- height: uint8
- base_image: uint16            # 基本画像インデックス

特性:
- allow_building: bool          # 建物と共存可か
- climatic_zone: uint8          # 気候地帯 (0=tropical, 1=temperate, 2=arctic)
```

### Way（道路・線路）

```
基本属性:
- object_id: string             # "TRACK_RAIL"
- name: string
- way_type: uint8               # 0=road, 1=rail, 2=canal, 3=monorail

容量:
- max_weight: uint16            # 最大積載重量
- maintenance_cost: uint16      # 年間維持費

仕様:
- max_speed: uint16             # 最大速度制限
- friction: uint8               # 摩擦係数
```

---

## Simutrans Squirrel API への対応

### マッピング例

| 仕様フィールド       | Squirrel API      | 説明               |
| -------------------- | ----------------- | ------------------ |
| `object_id`          | `name`            | オブジェクト識別子 |
| `name`               | `get_name()`      | 表示名             |
| `version`            | `get_version()`   | バージョン         |
| `max_speed`          | `get_max_speed()` | 最大速度           |
| `passenger_capacity` | `get_capacity()`  | 定員               |

### 例: Vehicle

```php
// Squirrel API
$vehicle->get_name();           // "Deutsche Bahn 101"
$vehicle->get_max_speed();      // 200 (km/h)
$vehicle->get_capacity();       // 450 (passengers)
$vehicle->get_weight();         // 80 (tons)
$vehicle->get_power();          // 6400 (kW)

// 標準フィールド
[
    'object_id' => 'DB-101',
    'name' => 'Deutsche Bahn 101',
    'max_speed' => 200,
    'passenger_capacity' => 450,
    'weight' => 80,
    'power' => 6400,
]
```

---

## データ型

### スカラー型

| 型       | サイズ   | 範囲                     | 例              |
| -------- | -------- | ------------------------ | --------------- |
| `uint8`  | 1 byte   | 0 - 255                  | `1`, `255`      |
| `uint16` | 2 bytes  | 0 - 65535                | `1000`, `65535` |
| `uint32` | 4 bytes  | 0 - 4294967295           | `100000`        |
| `int8`   | 1 byte   | -128 - 127               | `-1`, `127`     |
| `int16`  | 2 bytes  | -32768 - 32767           | `-1000`         |
| `int32`  | 4 bytes  | -2147483648 - 2147483647 | `-1000000`      |
| `float`  | 4 bytes  | IEEE 754                 | `3.14`, `-2.5`  |
| `bool`   | 1 byte   | 0/1                      | `true`, `false` |
| `string` | variable | text                     | `"DB-101"`      |

### 複合型

```php
// Array（配列）
'wagon_types' => [
    ['type' => 0, 'capacity' => 100],
    ['type' => 1, 'capacity' => 80],
]

// Object（構造体）
'power_info' => [
    'engine_power' => 6400,
    'tractive_effort' => 300,
    'gear_ratio' => 3.5,
]
```

---

## 列挙型定義

### VehicleType

```php
enum VehicleType: int {
    RAIL = 0,       # 鉄道
    ROAD = 1,       # 道路
    WATER = 2,      # 水上
    AIR = 3,        # 航空
    MONORAIL = 4,   # モノレール
}
```

### CargoType

```php
enum CargoType: int {
    PASSENGER = 0,
    MAIL = 1,
    CARGO = 2,
    LIVESTOCK = 3,
    COAL = 4,
    OIL = 5,
    ORE = 6,
    SCRAP = 7,
    STEEL = 8,
    // ... その他の貨物種別
}
```

### BuildingType

```php
enum BuildingType: int {
    STATION = 0,
    FACTORY = 1,
    CITY_BUILDING = 2,
    PARKING = 3,
}
```

### ClimaticZone

```php
enum ClimaticZone: int {
    TROPICAL = 0,
    TEMPERATE = 1,
    ARCTIC = 2,
    MEDITERRANEAN = 3,
}
```

---

## メタデータ構造

### 完全な Vehicle メタデータ例

```json
{
  "type": "vehicle",
  "object_id": "DB-101",
  "name": "Deutsche Bahn 101",
  "author": "Simutrans Community",
  "version": "0x0101",
  "vehicle_type": 0,
  "year_from": 1985,
  "year_to": 2005,
  "max_speed": 200,
  "weight": 80,
  "power": 6400,
  "tractive_effort": 300,
  "passenger_capacity": 450,
  "cargo_capacity": 0,
  "mail_capacity": 0,
  "has_engine": true,
  "powered": true,
  "passenger": true,
  "freight": false,
  "graphics": {
    "base_image": 0,
    "width": 1,
    "height": 8
  }
}
```

### 完全な Building メタデータ例

```json
{
  "type": "building",
  "object_id": "STATION_1",
  "name": "Simple Station",
  "author": "Simutrans Community",
  "version": "0x0100",
  "width": 3,
  "height": 3,
  "depth": 1,
  "building_type": 0,
  "capacity": 300,
  "mail_capacity": 50,
  "building_cost": 50000,
  "maintenance_cost": 500,
  "removal_cost": 10000,
  "cargo_types": [0, 1],
  "graphics": {
    "base_image": 0,
    "seasons": 4
  }
}
```

---

## 命名規則の一貫性

### チェックリスト

- [ ] フィールド名がスネークケース
- [ ] 言語が英語
- [ ] プレフィックス / サフィックスが標準に従っている
- [ ] bool 型フィールドが `is_` または `has_` で始まっている
- [ ] 数値フィールドに単位が明記されている（コメント）
- [ ] null 許容フィールドが明示的に `?type` になっている

---

## 拡張性

### 新しいフィールドの追加

新しいフィールドを追加する場合:

1. **スネークケース命名規則に従う**
2. **プレフィックス / サフィックスを適切に選択**
3. **型とデータ範囲を明記**
4. **本ドキュメントのこのセクションを更新**

### 例: 新しいフィールドの追加

```php
// 追加前
[
    'passenger_capacity' => 450,
]

// 追加後
[
    'passenger_capacity' => 450,
    'disabled_access' => true,      // 新規フィールド
    'climate_zones' => [0, 1],      // 新規フィールド
]

// ドキュメント更新
/*
- disabled_access: bool            # 身障者対応か
- climate_zones: uint8[]           # 対応気候地帯
*/
```

---

## 参考リンク

- **使用方法**: [PAK Parser Usage](./pak-parser-usage-20260103-knowledge.md)
- **実装詳細**: [PAK Parser Implementation](./pak-parser-implementation-20260103-knowledge.md)
- **バイナリフォーマット**: [PAK Tunnel Format](./pak-tunnel-format-spec.md)
- **Simutrans公式**: https://wiki.simutrans.com/

---

**最終更新**: 2025-11-24  
**バージョン**: 1.0.0  
**メンテナー**: Simutrans Community
