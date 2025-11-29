# Pak Parser Field Naming Standards

This document defines the naming conventions for Pak parser output fields.

## Principles

All parser output field names follow the **Simutrans Squirrel API** naming conventions for consistency with:

- Official Simutrans scripting interface (Squirrel)
- Content creator expectations (.dat file format)
- Public-facing API documentation

## Architecture

**Parsers output numeric fields only.** String conversions are handled by frontend formatters.

```php
// ✅ Parsers only output numeric fields
$data['waytype'] = $reader->readUint8();
// Frontend converts on-demand using formatWayType()
```

**Rationale:**

- **Data size reduction**: Eliminates 10-30 bytes per string field
- **Separation of concerns**: Backend stores data, frontend handles presentation
- **Flexibility**: Frontend can localize without backend changes

## Field Naming Rules

### 1. Waytype Fields

**Use full word `waytype` instead of abbreviated `wtyp`**

```php
// ✅ Correct
$data['waytype'] = $reader->readUint8();

// ❌ Incorrect
$data['wtyp'] = $reader->readUint8();
```

**Rationale:**

- Squirrel API uses `get_waytype()` method
- .dat files use `waytype=` parameter
- More readable for API consumers

**Related fields:**

- `waytype` (integer) - Numeric waytype value
- `own_waytype` (integer) - For way-objects (catenary)

### 2. Engine Type Fields

**Use underscore-separated `engine_type` instead of camelCase**

```php
// ✅ Correct
$data['engine_type'] = $reader->readUint8();
```

**Rationale:**

- .dat files use `engine_type=` parameter
- Consistent with other field names (snake_case)

### 3. ~~String Conversion Suffix~~ (Deprecated)

**String conversion fields have been removed. Use frontend formatters instead.**

````typescript
// ✅ Frontend formatting
import { formatWayType } from "@/features/articles/components/pak/formatter";
const waytypeName = formatWayType(vehicleData.waytype);

// ❌ Removed from backend
### 3. Other Field Names

## Frontend Formatting Functions

String conversions are now handled by frontend formatter functions.

### Location

**File:** `resources/js/features/articles/components/pak/formatter.ts`

### Available Formatters

```typescript
// Waytype conversion
export function formatWayType(waytype: number | undefined): string;

// Good category conversion
export function formatGoodCategory(catg: number | undefined): string;

// Date formatting
export function formatDate(value: number | undefined): string;

// Numeric formatting
export const formatSpeed = (speed: number | undefined): string;
export const formatPower = (power: number | undefined): string;
export const formatPrice = (price: number | undefined): string;
export const formatRunningCost = (cost: number | undefined): string;
export const formatMaintenanceCost = (cost: number | undefined): string;
````

### Additional Conversions

**File:** `resources/js/features/articles/components/pak/pakBuildingTranslations.ts`

```typescript
// Building type conversion (supports both number and string)
export function getBuildingTypeName(type: string | number): string;

// Enables flags conversion (bitfield to Japanese)
export function getEnablesString(enables: string | number): string;

// Placement type conversion
export function getPlacementName(placement: string | number): string;

// System type conversion
export function getSystemTypeName(type: string | number): string;
```

**File:** `resources/js/features/articles/components/pak/pakTranslations.ts`

```typescript
// Engine type conversion
export function getEngineTypeName(type: string | number): string;

// Freight type conversion
export function getFreightTypeName(type: string): string;
```

## ~~Converter Utility Usage~~ (Deprecated)

**The following converter utilities are no longer used for parser output:**

- ~~`WayTypeConverter::getWayTypeName()`~~ - Use `formatWayType()` in frontend
- ~~`EngineTypeConverter::convert()`~~ - Use `getEngineTypeName()` in frontend
- ~~`BuildingTypeConverter::getBuildingTypeName()`~~ - Use `getBuildingTypeName()` in frontend
- ~~`BuildingTypeConverter::getEnablesString()`~~ - Use `getEnablesString()` in frontend

**Note:** These utilities still exist in backend code but are not used in parser output.

## Parser Implementation Pattern

## Parser Implementation Pattern - numeric fields only

            $data = [
                'waytype' => $reader->readUint8(),
                'engine_type' => $reader->readUint8(),
                'intro_date' => $reader->readUint16LE(),
                'retire_date' => $reader->readUint16LE(),
                // ... other numeric fields
            ];
        try {
            $reader = new BinaryReader($node->data);

            // Parse binary data
            $data = [
                'waytype' => $reader->readUint8(),
                'engine_type' => $reader->readUint8(),
                'intro_date' => $reader->readUint16LE(),
                'retire_date' => $reader->readUint16LE(),
                // ... other fields
            ];

            return $data;

**Naming convention:**

- Use same field names as PHP parsers
- Mark all fields as optional (`?`)
- **Do not include `*_str` fields**

**Example:**

````typescript
export interface VehicleData {
  waytype?: number;
  // waytype_str removed - use formatWayType()
  engine_type?: number;
  // engine_type_str removed - use getEngineTypeName()
**Naming convention:**

- Use same field names as PHP parsers
- Mark all fields as optional (`?`)
- Numeric fields only (no `*_str` fields)

**Example:**

```typescript
export interface VehicleData {
  waytype?: number;
  engine_type?: number;
  // ... other fields
}

export interface wayobjData {
  waytype?: number;
  own_waytype?: number;
  // ... other fields
}

export interface BuildingData {
  type?: number;
  enables?: number;
  allowed_climates?: number;
  // ... other fields
}
```own_wtyp` → `own_waytype`
- `own_wtyp_str` → `own_waytype_str` (then removed in Phase 2)

#### Phase 2: String Field Removal

All `*_str` suffix fields were removed from parser output:

**Removed fields:**

## References---

**Last Updated:** 2025-11-29
````
