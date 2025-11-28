# Pak Parser Field Naming Standards

This document defines the naming conventions for Pak parser output fields.

## Principles

All parser output field names follow the **Simutrans Squirrel API** naming conventions for consistency with:

- Official Simutrans scripting interface (Squirrel)
- Content creator expectations (.dat file format)
- Public-facing API documentation

## Field Naming Rules

### 1. Waytype Fields

**Use full word `waytype` instead of abbreviated `wtyp`**

```php
// ✅ Correct
$data['waytype'] = $reader->readUint8();
$data['waytype_str'] = WayTypeConverter::getWayTypeName($data['waytype']);

// ❌ Incorrect
$data['wtyp'] = $reader->readUint8();
$data['wtyp_str'] = WayTypeConverter::getWayTypeName($data['wtyp']);
```

**Rationale:**

- Squirrel API uses `get_waytype()` method
- .dat files use `waytype=` parameter
- More readable for API consumers

**Related fields:**

- `waytype` (integer) - Numeric waytype value
- `waytype_str` (string) - Human-readable waytype name
- `own_waytype` (integer) - For way-objects (catenary)
- `own_waytype_str` (string) - String representation of own_waytype

### 2. Engine Type Fields

**Use underscore-separated `engine_type` instead of camelCase**

```php
// ✅ Correct
$data['engine_type'] = $reader->readUint8();
$data['engine_type_str'] = EngineTypeConverter::convert($data['engine_type']);
```

**Rationale:**

- .dat files use `engine_type=` parameter
- Consistent with other field names (snake_case)

### 3. String Conversion Suffix

**Always use `_str` suffix for string conversions, never `_name`**

```php
// ✅ Correct
$data['catg_str'] = self::CATEGORY_NAMES[$catg];
$data['waytype_str'] = WayTypeConverter::getWayTypeName($waytype);
$data['engine_type_str'] = EngineTypeConverter::convert($engineType);

// ❌ Incorrect
$data['catg_name'] = self::CATEGORY_NAMES[$catg];
$data['waytype_name'] = WayTypeConverter::getWayTypeName($waytype);
```

**Rationale:**

- Consistency across 18+ parser output fields
- Clear distinction between numeric value and string representation

### 4. Other Field Names

**Use snake_case for all field names**

```php
$data['intro_date'] = $reader->readUint16LE();
$data['retire_date'] = $reader->readUint16LE();
$data['running_cost'] = $reader->readUint16LE();
$data['axle_load'] = $reader->readUint16LE();
$data['freight_type'] = $freightType;
```

## Converter Utility Usage

### WayTypeConverter

**Method:** `getWayTypeName(int $waytype): string`

**Usage:**

```php
if (isset($data['waytype'])) {
    assert(is_int($data['waytype']));
    $data['waytype_str'] = WayTypeConverter::getWayTypeName($data['waytype']);
}
```

**Supported waytypes:**

- 0 => 'ignore'
- 1 => 'road'
- 2 => 'track'
- 3 => 'water'
- 4 => 'overheadlines'
- 5 => 'monorail'
- 6 => 'maglev'
- 7 => 'tram'
- 8 => 'narrowgauge'
- 16 => 'air'
- 128 => 'powerline'
- 255 => 'any'

### EngineTypeConverter

**Method:** `convert(int $engineType): string`

**Usage:**

```php
if (isset($data['engine_type'])) {
    assert(is_int($data['engine_type']));
    $data['engine_type_str'] = EngineTypeConverter::convert($data['engine_type']);
}
```

**Supported engine types:**

- 0 => 'steam'
- 1 => 'diesel'
- 2 => 'electric'
- 3 => 'bio'
- 4 => 'sail'
- 5 => 'fuel_cell'
- 6 => 'hydrogene'
- 7 => 'battery'

### BuildingTypeConverter

**Methods:**

- `getBuildingTypeName(int $type): string`
- `getEnablesString(int $enables): string`

**Usage:**

```php
$data['type_str'] = BuildingTypeConverter::getBuildingTypeName($data['type']);
$data['enables_str'] = BuildingTypeConverter::getEnablesString($data['enables']);

// For waytype conversion, use WayTypeConverter
$data['waytype_str'] = WayTypeConverter::getWayTypeName($data['waytype']);
```

## Parser Implementation Pattern

### Complete Example

```php
final readonly class ExampleParser implements TypeParserInterface
{
    #[\Override]
    public function parse(Node $node): ?array
    {
        try {
            $reader = new BinaryReader($node->data);

            // Parse binary data
            $data = [
                'waytype' => $reader->readUint8(),
                'engine_type' => $reader->readUint8(),
                // ... other fields
            ];

            // Apply string conversions at the end of parse()
            if (isset($data['waytype'])) {
                assert(is_int($data['waytype']));
                $data['waytype_str'] = WayTypeConverter::getWayTypeName($data['waytype']);
            }

            if (isset($data['engine_type'])) {
                assert(is_int($data['engine_type']));
                $data['engine_type_str'] = EngineTypeConverter::convert($data['engine_type']);
            }

            return $data;
        } catch (\Throwable) {
            return null;
        }
    }
}
```

## TypeScript Type Definitions

**File:** `resources/js/types/models/FileInfo.ts`

**Naming convention:**

- Use same field names as PHP parsers
- Mark all fields as optional (`?`)

**Example:**

```typescript
export interface VehicleData {
  waytype?: number;
  waytype_str?: string;
  engine_type?: number;
  engine_type_str?: string;
  // ... other fields
}

export interface WayObjectData {
  waytype?: number;
  waytype_str?: string;
  own_waytype?: number;
  own_waytype_str?: string;
  // ... other fields
}
```

## Migration Notes

### Breaking Changes (2025-11-29)

The following field names were changed for Squirrel API compliance:

**PHP Parsers:**

- `wtyp` → `waytype`
- `wtyp_str` → `waytype_str`
- `own_wtyp` → `own_waytype`
- `own_wtyp_str` → `own_waytype_str`
- `catg_name` → `catg_str`

**TypeScript Types:**

- Same changes as PHP parsers

**Frontend Code:**

- `obj.wayData.wtyp_str` → `obj.wayData.waytype_str`

### Data Migration

All existing parsed data will be re-parsed with new field names. No manual data migration is required.

## References

- **Simutrans Source:** `simutrans/descriptor/reader/vehicle_reader.cc`
- **Squirrel API:** `script/api/squirrel_types_*.awk`
- **.dat Format:** Content creator documentation

---

**Last Updated:** 2025-11-29
**Version:** 1.0.0
