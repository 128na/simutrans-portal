# Simutrans .pak Tunnel Format (TUNN) Specification

This document provides a comprehensive specification of the Simutrans tunnel format based on analysis of the source code in `simutrans/descriptor/reader/tunnel_reader.cc` and related files.

## Node Identifier

**4-byte identifier**: `TUNL` (0x54554E4C)

- Defined in `simutrans/descriptor/objversion.h` as: `obj_tunnel = C4ID('T','U','N','L')`
- Note: Different from the string "TUNN" - actual identifier is "TUNL"

## Supported Versions

Tunnel nodes support versions **0 through 6**:

- **Version 0**: Legacy format (no version stamp, extremely rare)
- **Version 1**: First versioned format (introduced basic properties)
- **Version 2**: Added snow/seasonal images support
- **Version 3**: Added underground way image support
- **Version 4**: Added broad portal support
- **Version 5**: Added axle load property
- **Version 6**: Changed cost/maintenance to sint64 (current version)

The writer currently generates **version 5** (`0x8005`), but the reader supports up to version 6.

## Version Detection

The first uint16 in the data determines the format:

- **Bit 15 set (0x8000)**: Versioned format - mask with `0x7FFF` to get version number
- **Bit 15 clear**: Legacy version 0 (waytype stored in first uint16)

## Node Structure

### Child Nodes

Tunnels have a complex child node structure similar to bridges:

```
0   Name (text_desc_t)
1   Copyright (text_desc_t)
2   Image-list Background
3   Image-list Foreground
4   Cursor/Icon (skin_desc_t)
[5  Image-list Background - snow]  (if number_of_seasons == 1)
[6  Image-list Foreground - snow]  (if number_of_seasons == 1)
[7 or 5  Underground way]          (if has_way == 1)
```

The underground way child node index is calculated as: `5 + number_of_seasons * 2`

### Image Organization

Unlike bridges, tunnels use **slope-based indexing** rather than ribi-based:

Each image list contains images for 4 slopes, with optional portal variations:

- **4 base slopes**: South (36), North (4/8), West (12/24), East (28/56)
- **Portal types** (if broad_portals == 1): normal, left, right, middle (4 variations per slope)
- Total images per season per layer: `4 slopes × 4 portal types = 16 images`

Slope indices mapping (from `tunnel_desc.cc`):

```
slope  4/8:  North slope  -> index 1
slope 12/24: West slope   -> index 2
slope 28/56: East slope   -> index 3
slope 36/72: South slope  -> index 0
```

## Version-by-Version Binary Format

### Version 0 (Legacy - No Version Stamp)

**Extremely rare**. If found, the tunnel is converted to reasonable defaults by `convert_old_tunnel()`.

**Binary Structure**: Empty or minimal data

**Default conversions**:

- If name is "RoadTunnel": wtyp=road_wt, topspeed=120
- Otherwise: wtyp=track_wt, topspeed=280
- maintenance=500, price=200000
- intro_date=DEFAULT_INTRO_DATE*12, retire_date=DEFAULT_RETIRE_DATE*12
- has_way=false, axle_load=9999

### Version 1 (First Versioned)

**Added**: Basic versioning with core properties

**Binary Structure** (13 bytes after version):

```
Offset  Size  Type    Field           Description
------  ----  ------  --------------  ----------------------------------
0       2     uint16  version         0x8001 (high bit set)
2       4     uint32  topspeed        Maximum speed in km/h
6       4     uint32  price           Construction cost (1/100 credits)
10      4     uint32  maintenance     Monthly maintenance cost
14      1     uint8   wtyp            Way type
15      2     uint16  intro_date      Introduction date (months)
17      2     uint16  retire_date     Retirement date (months)
```

**Defaults**:

- number_of_seasons=0
- has_way=0
- broad_portals=0
- axle_load=9999

### Version 2 (Snow Images)

**Added**: Seasonal graphics support

**Binary Structure** (20 bytes after version):

```
Offset  Size  Type    Field               Description
------  ----  ------  ------------------  ----------------------------------
0       2     uint16  version             0x8002
2       4     uint32  topspeed            Maximum speed in km/h
6       4     uint32  price               Construction cost
10      4     uint32  maintenance         Monthly maintenance cost
14      1     uint8   wtyp                Way type
15      2     uint16  intro_date          Introduction date (months)
17      2     uint16  retire_date         Retirement date (months)
19      1     uint8   number_of_seasons   0=no snow, 1=snow images present
```

**Defaults**:

- has_way=0
- broad_portals=0
- axle_load=9999

### Version 3 (Underground Way)

**Added**: Support for underground way graphics (way visible inside tunnel)

**Binary Structure** (21 bytes after version):

```
Offset  Size  Type    Field               Description
------  ----  ------  ------------------  ----------------------------------
0       2     uint16  version             0x8003
2       4     uint32  topspeed            Maximum speed in km/h
6       4     uint32  price               Construction cost
10      4     uint32  maintenance         Monthly maintenance cost
14      1     uint8   wtyp                Way type
15      2     uint16  intro_date          Introduction date (months)
17      2     uint16  retire_date         Retirement date (months)
19      1     uint8   number_of_seasons   0=no snow, 1=snow images
20      1     uint8   has_way             0=no way, 1=way xref present
```

**Defaults**:

- broad_portals=0
- axle_load=9999

### Version 4 (Broad Portals)

**Added**: Support for broad portal variations (left/right/middle portal types)

**Binary Structure** (22 bytes after version):

```
Offset  Size  Type    Field               Description
------  ----  ------  ------------------  ----------------------------------
0       2     uint16  version             0x8004
2       4     uint32  topspeed            Maximum speed in km/h
6       4     uint32  price               Construction cost
10      4     uint32  maintenance         Monthly maintenance cost
14      1     uint8   wtyp                Way type
15      2     uint16  intro_date          Introduction date (months)
17      2     uint16  retire_date         Retirement date (months)
19      1     uint8   number_of_seasons   0=no snow, 1=snow images
20      1     uint8   has_way             0=no way, 1=way xref present
21      1     uint8   broad_portals       0=standard, 1=broad (4 portal types)
```

**Defaults**:

- axle_load=9999

### Version 5 (Axle Load)

**Added**: Axle load limit for heavy vehicles

**Binary Structure** (24 bytes after version):

```
Offset  Size  Type    Field               Description
------  ----  ------  ------------------  ----------------------------------
0       2     uint16  version             0x8005
2       4     uint32  topspeed            Maximum speed in km/h
6       4     uint32  price               Construction cost
10      4     uint32  maintenance         Monthly maintenance cost
14      1     uint8   wtyp                Way type
15      2     uint16  intro_date          Introduction date (months)
17      2     uint16  retire_date         Retirement date (months)
19      2     uint16  axle_load           Maximum axle load (tons)
21      1     uint8   number_of_seasons   0=no snow, 1=snow images
22      1     uint8   has_way             0=no way, 1=way xref present
23      1     uint8   broad_portals       0=standard, 1=broad
```

**Current writer version** - this is what the writer generates.

### Version 6 (64-bit Costs)

**Changed**: price and maintenance upgraded to sint64 for large values

**Binary Structure** (32 bytes after version):

```
Offset  Size  Type    Field               Description
------  ----  ------  ------------------  ----------------------------------
0       2     uint16  version             0x8006
2       4     uint32  topspeed            Maximum speed in km/h
6       8     sint64  price               Construction cost (1/100 credits)
14      8     sint64  maintenance         Monthly maintenance cost
22      1     uint8   wtyp                Way type
23      2     uint16  intro_date          Introduction date (months)
25      2     uint16  retire_date         Retirement date (months)
27      2     uint16  axle_load           Maximum axle load (tons)
29      1     uint8   number_of_seasons   0=no snow, 1=snow images
30      1     uint8   has_way             0=no way, 1=way xref present
31      1     uint8   broad_portals       0=standard, 1=broad
```

## Core Properties

### Way Type (wtyp)

- **Type**: uint8
- **Description**: The way type the tunnel can be built on
- **Values**: See waytype enumeration (track_wt, road_wt, water_wt, etc.)

### Top Speed (topspeed)

- **Type**: uint32 (all versions)
- **Unit**: km/h
- **Description**: Maximum allowed speed through the tunnel
- **Special**: If 0 in legacy format, indicates conversion needed

### Price

- **Type**: uint32 (versions 1-5), sint64 (version 6)
- **Unit**: 1/100 credits per tile
- **Description**: Construction cost
- **Default**: 200000 (legacy conversion)

### Maintenance

- **Type**: uint32 (versions 1-5), sint64 (version 6)
- **Unit**: Monthly cost at bits_per_month=18
- **Description**: Recurring monthly maintenance cost
- **Default**: 500 (legacy conversion)

### Introduction Date (intro_date)

- **Type**: uint16
- **Unit**: Months since game start
- **Description**: Date when tunnel becomes available
- **Calculation**: `year * 12 + (month - 1)`
- **Default**: DEFAULT_INTRO_DATE\*12

### Retirement Date (retire_date)

- **Type**: uint16
- **Unit**: Months since game start
- **Description**: Date when tunnel becomes obsolete
- **Calculation**: `year * 12 + (month - 1)`
- **Default**: DEFAULT_RETIRE_DATE\*12

### Axle Load (axle_load)

- **Type**: uint16
- **Since**: Version 5
- **Unit**: Tons
- **Description**: Maximum axle load of vehicles that can use the tunnel
- **Default**: 9999 (no limit)

### Number of Seasons (number_of_seasons)

- **Type**: uint8 (sint8 internally)
- **Since**: Version 2
- **Values**: 0=no seasonal variation, 1=snow images present
- **Description**: Controls whether seasonal graphics are used

### Has Way (has_way)

- **Type**: uint8
- **Since**: Version 3
- **Values**: 0=no, 1=yes
- **Description**: Whether underground way graphics are present (way visible inside tunnel)

### Broad Portals (broad_portals)

- **Type**: uint8
- **Since**: Version 4
- **Values**: 0=standard portal, 1=broad portals (4 variations)
- **Description**: Enables left/right/middle portal variations for wider tunnels

## Special Features

### Underground Way Graphics

- Controlled by `has_way` property (version 3+)
- When enabled, a child node with way graphics is included
- Shows the way/track inside the tunnel
- Child node is an xref to a way_desc_t object

### Broad Portals

- Controlled by `broad_portals` property (version 4+)
- Provides 4 portal variations per direction:
  - Normal portal (standard entrance)
  - Left portal (for wide tunnels)
  - Right portal (for wide tunnels)
  - Middle portal (for very wide tunnels)
- Image naming: `frontimage[n]`, `frontimage[nl]`, `frontimage[nr]`, `frontimage[nm]`

### No Offset Property

- Unlike bridges, tunnels do **NOT** have an offset property
- Child nodes are always in standard positions
- No special handling for old vs new name/copyright lookup

## Key Differences from Bridge Format

| Feature                  | Bridge                                      | Tunnel                             |
| ------------------------ | ------------------------------------------- | ---------------------------------- |
| **Identifier**           | `BRDG`                                      | `TUNL`                             |
| **Image Indexing**       | Ribi-based (directional)                    | Slope-based (4 slopes)             |
| **Offset Property**      | Yes (versions 0-7 vs 8+)                    | No                                 |
| **Pillar Properties**    | Yes (pillars_every, asymmetric, max_height) | No                                 |
| **Max Length**           | Yes                                         | No                                 |
| **Portal Variations**    | No                                          | Yes (broad_portals)                |
| **Underground Graphics** | No                                          | Yes (has_way)                      |
| **Current Version**      | 10 (64-bit costs)                           | 5 (writer), 6 (reader)             |
| **Image Organization**   | 12 or 24 images (pillar variations)         | 4 or 16 images (portal variations) |
| **Child Node Order**     | Foreground, Background, Cursor              | Background, Foreground, Cursor     |

### Child Node Order Difference

**Bridge**:

```
0   Foreground
1   Background
2   Cursor
3   Foreground-snow (optional)
4   Background-snow (optional)
```

**Tunnel**:

```
0   Name
1   Copyright
2   Background
3   Foreground
4   Cursor
5   Background-snow (optional)
6   Foreground-snow (optional)
7   Underground way (optional)
```

## Implementation Notes

### Reading Version

```php
$firstUint16 = unpack('v', substr($binaryData, 0, 2))[1];
if (($firstUint16 & 0x8000) !== 0) {
    $version = $firstUint16 & 0x7FFF;
} else {
    $version = 0; // Legacy format
}
```

### Date Conversion

```php
// Binary format: months since game start
// Human format: year and month
$year = intdiv($intro_date, 12);
$month = ($intro_date % 12) + 1;
```

### Legacy Detection

If `topspeed == 0` after reading, the tunnel needs conversion using `convert_old_tunnel()` logic.

### Way Type Values

Common values:

- `0` = track_wt (railways)
- `1` = road_wt (roads)
- `2` = water_wt (canals)
- `3` = monorail_wt
- `4` = maglev_wt
- `16` = narrowgauge_wt

Use `WayTypeConverter` to map values to human-readable names.

## References

Source files analyzed:

- `simutrans/descriptor/reader/tunnel_reader.cc` - Binary reading logic
- `simutrans/descriptor/reader/tunnel_reader.h` - Reader interface
- `simutrans/descriptor/tunnel_desc.h` - Descriptor structure and child nodes
- `simutrans/descriptor/tunnel_desc.cc` - Slope indices mapping
- `simutrans/descriptor/writer/tunnel_writer.cc` - Binary writing logic (version 5)
- `simutrans/descriptor/objversion.h` - Object type identifiers
- `simutrans/descriptor/obj_base_desc.h` - Base infrastructure properties

## Summary for TunnelParser Implementation

To implement `TunnelParser.php`:

1. **Detect version** from first uint16 (check bit 15)
2. **Parse based on version**:
   - Version 0: Apply legacy conversion defaults
   - Versions 1-5: Read sequential fields (uint32 for costs)
   - Version 6: Read sequential fields (sint64 for costs)
3. **Apply defaults** for fields not present in older versions
4. **Convert dates** from months to year/month format
5. **Use WayTypeConverter** for human-readable waytype names
6. **Handle child nodes** for images, cursor, and optional way reference
7. **Note**: Unlike bridges, tunnels have no offset calculation for child nodes

**Key validation points**:

- If version > 6: Throw error (unsupported)
- If topspeed == 0 after reading: Apply legacy conversion
- If version < 5: Set axle_load = 9999
- Check node.size before reading to detect empty/legacy format
