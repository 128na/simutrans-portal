# Pak Parser Implementation Summary

## Overview

Complete implementation of Simutrans .pak binary file parser for extracting addon metadata (name, copyright, object type, compiler version).

## Implementation Date

2024-11-24

## Components Implemented

### 1. Exception

- **File**: `app/Exceptions/InvalidPakFileException.php`
- **Purpose**: Custom exception for pak parsing errors
- **Factory Methods**:
  - `invalidHeader()` - Invalid header format
  - `corruptedNode()` - Corrupted node structure
  - `unexpectedEof()` - Unexpected end of file
  - `unsupportedVersion()` - Unsupported compiler version

### 2. Binary Reader

- **File**: `app/Services/FileInfo/Extractors/Pak/BinaryReader.php`
- **Purpose**: Little-endian binary data reader
- **Methods**:
  - `readUint8()` - Read 1-byte unsigned integer
  - `readUint16LE()` - Read 2-byte little-endian unsigned integer
  - `readUint32LE()` - Read 4-byte little-endian unsigned integer
  - `readString($length)` - Read fixed-length string
  - `readNullTerminatedString()` - Read string until null byte
  - `skip($bytes)`, `seek($position)`, `hasMore()`

### 3. Header Parser

- **File**: `app/Services/FileInfo/Extractors/Pak/PakHeader.php`
- **Purpose**: Parse and validate pak file header
- **Format**:
  ```
  "Simutrans object file\n"           (22 bytes)
  "Compiled with SimObjects X.X.X\n"  (variable length)
  0x1A                                 (1 byte separator)
  uint32 version_code                  (4 bytes little-endian)
  ```
- **Returns**: `PakHeader` with `compilerVersion` string and `compilerVersionCode` integer

### 4. Node Structure

- **File**: `app/Services/FileInfo/Extractors/Pak/Node.php`
- **Purpose**: Represent pak file node structure
- **Format**:
  ```
  char[4] type      (4 ASCII characters)
  uint16 children   (number of child nodes)
  uint16 size       (data size, 0xFFFF = extended)
  [uint32 size]     (extended size if size == 0xFFFF)
  char[] data       (node data)
  Node[] children   (child nodes)
  ```
- **Node Types** (as 4-char ASCII strings):
  - `TEXT` (0x54455854) - Text/name node
  - `ROOT` (0x544F4F52) - Root container
  - `VHCL` (0x56484C4C) - Vehicle
  - `BUIL` (0x4C495542) - Building
  - `BRDG` (0x47444252) - Bridge
  - `WAY\0` (0x57415900) - Way (road/rail) - Note: 3 chars + null byte
  - `TREE` (0x45455254) - Tree
  - `GOOD` (0x444F4F47) - Good (cargo)
- **Safety**: Max depth limit of 100 to prevent stack overflow

### 5. Object Type Converter

- **File**: `app/Services/FileInfo/Extractors/Pak/ObjectTypeConverter.php`
- **Purpose**: Convert 4-char ASCII type codes to human-readable strings
- **Mapping**:
  - `VHCL` → `vehicle`
  - `BUIL` → `building`
  - `BRDG` → `bridge`
  - `WAY\0` → `way`
  - `TREE` → `tree`
  - `GOOD` → `good`
  - Other → `unknown_{TYPE}`

### 6. Metadata Value Object

- **File**: `app/Services/FileInfo/Extractors/Pak/PakMetadata.php`
- **Purpose**: Value object for pak metadata
- **Properties**:
  - `name` (string) - Addon name
  - `copyright` (string|null) - Copyright/author
  - `objectType` (string) - Object type (vehicle, building, etc.)
  - `compilerVersionCode` (int) - Compiler version code
- **Methods**:
  - `fromNode(Node, int)` - Extract metadata from node
  - `toArray()` - Convert to associative array for JSON

### 7. Main Parser

- **File**: `app/Services/FileInfo/Extractors/Pak/PakParser.php`
- **Purpose**: Main orchestrator for pak parsing
- **Method**: `parse(string $binary)` returns:
  ```php
  [
      'names' => ['addon_name1', 'addon_name2', ...],
      'metadata' => [
          [
              'name' => 'addon_name1',
              'copyright' => 'Author Name',
              'objectType' => 'vehicle',
              'compilerVersionCode' => 1003
          ],
          ...
      ]
  ]
  ```
- **Process**:
  1. Parse header
  2. Parse root node recursively
  3. Find TEXT nodes (name/copyright)
  4. Extract metadata for each named object
  5. Return names array (backward compatibility) + metadata array

### 8. Extractor Integration

- **File**: `app/Services/FileInfo/Extractors/PakExtractor.php`
- **Changes**:
  - Inject `PakParser` dependency
  - Try new parser first
  - Catch `InvalidPakFileException` and fall back to legacy `PakBinary` scanner
  - Log warnings on fallback
  - Return extended structure: `['names' => [...], 'metadata' => [...]]`

### 9. Service Integration

- **File**: `app/Services/FileInfo/FileInfoService.php`
- **Changes**:
  - Check if extractor is `PakExtractor`
  - Check if result has `metadata` key
  - Store names in `$fileInfo->data['pak']` (backward compatibility)
  - Store metadata in `$fileInfo->data['paks_metadata']`
  - Format: `['pak_filename.pak' => [metadata1, metadata2, ...]]`

### 10. TypeScript Types

- **File**: `resources/js/types/models/FileInfo.ts`
- **Changes**:

  ```typescript
  interface PakMetadata {
    name: string;
    copyright: string | null;
    objectType: string;
    compilerVersionCode: number;
  }

  interface FileInfo {
    // ...existing fields
    data: {
      pak?: string[]; // Legacy names array
      paks_metadata?: Record<string, PakMetadata[]>; // New metadata
      // ...other data
    };
  }
  ```

### 11. Batch Reparse Command

- **File**: `app/Console/Commands/Attachment/ReparsePakFilesCommand.php`
- **Command**: `php artisan attachment:reparse-pak-files`
- **Options**:
  - `--limit=N` - Limit number of files to reparse
  - `--dry-run` - Show files without reparsing
- **Features**:
  - Uses cursor for memory efficiency
  - Progress bar
  - Error logging
  - Summary report

### 12. Tests

- **File**: `tests/Unit/Services/FileInfo/Extractors/Pak/PakParserTest.php`
- **Tests**:
  - `test_parse_existing_file()` - Parse actual .pak file
  - `test_invalid_header()` - Reject invalid header
  - `test_empty_file()` - Reject empty file
  - `test_metadata_structure()` - Verify metadata structure

- **File**: `tests/Unit/Services/FileInfo/Extractors/PakExtractorTest.php`
- **Updated Tests**:
  - `test_extract()` - Verify new return structure
  - `test_fallback_on_invalid_file()` - Verify fallback to legacy scanner

## Binary Format Details

### Header Structure

```
Offset  Size  Type     Description
------  ----  -------  -----------
0       22    string   "Simutrans object file\n"
22      var   string   "Compiled with SimObjects X.X.X\n"
var     1     byte     0x1A (separator)
var+1   4     uint32   Version code (little-endian)
```

### Node Structure

```
Offset  Size  Type     Description
------  ----  -------  -----------
0       4     char[4]  Node type (ASCII, e.g., "VHCL", "BUIL")
4       2     uint16   Number of child nodes
6       2     uint16   Data size (or 0xFFFF if extended)
8       4     uint32   Extended data size (if size == 0xFFFF)
var     var   bytes    Node data
var     var   Node[]   Child nodes (recursive)
```

### Key Discovery: Type Codes are ASCII Strings

**Critical Issue Fixed**: Initial implementation read type codes as little-endian uint32, but they are actually stored as 4-character ASCII strings.

Example:

- File bytes: `56 48 43 4C` (hex)
- ASCII: "VHCL" (Vehicle)
- If read as uint32LE: 0x4C434856 ❌
- Correct: Read as 4-char string "VHCL" ✅

## Usage Examples

### Parse Single File

```php
$parser = app(PakParser::class);
$binary = file_get_contents('addon.pak');
$result = $parser->parse($binary);

// Result:
// [
//     'names' => ['vehicle_name'],
//     'metadata' => [
//         [
//             'name' => 'vehicle_name',
//             'copyright' => 'Author',
//             'objectType' => 'vehicle',
//             'compilerVersionCode' => 1003
//         ]
//     ]
// ]
```

### Batch Reparse Existing Files

```bash
# Dry run (show files without reparsing)
php artisan attachment:reparse-pak-files --dry-run

# Reparse first 10 files
php artisan attachment:reparse-pak-files --limit=10

# Reparse all files
php artisan attachment:reparse-pak-files
```

### Frontend Access (TypeScript)

```typescript
import type { FileInfo, PakMetadata } from "@/types/models";

function displayPakInfo(fileInfo: FileInfo) {
  const paksMetadata = fileInfo.data.paks_metadata;

  if (!paksMetadata) return;

  Object.entries(paksMetadata).forEach(([filename, metadataArray]) => {
    metadataArray.forEach((metadata: PakMetadata) => {
      console.log(`${metadata.name} (${metadata.objectType})`);
      console.log(`Author: ${metadata.copyright ?? "Unknown"}`);
      console.log(`Compiler: v${metadata.compilerVersionCode}`);
    });
  });
}
```

## Test Results

All tests passing (89 total unit tests):

- ✅ PakParserTest: 4/4 passed
- ✅ PakExtractorTest: 4/4 passed
- ✅ All other unit tests: 81/81 passed

## Debugging Notes

### Issue 1: Magic String Length

**Problem**: Expected 23 bytes, actual is 22 bytes
**Solution**: "Simutrans object file\n" is 22 bytes (21 chars + \n)

### Issue 2: Type Code Reading

**Problem**: Type codes read as little-endian uint32 resulted in reversed bytes
**Solution**: Read as 4-character ASCII string instead

### Issue 3: Hex Analysis

Used PowerShell to analyze actual file structure:

```powershell
$bytes = Get-Content -Path "file.pak" -Encoding Byte -TotalCount 70
$bytes | ForEach-Object { $_.ToString("X2") } | Join-String -Separator " "
```

Result confirmed:

- Magic: `53 69 6D...` = "Simutrans object file\n"
- Version: `43 6F 6D...` = "Compiled with SimObjects 0.1.3exp\n"
- Separator: `1A`
- Version code: `EB 03 00 00` = 0x000003EB = 1003
- Root node: `52 4F 4F 54` = "ROOT"

## Backward Compatibility

- ✅ Legacy `PakExtractor` returns extended structure with both `names` and `metadata`
- ✅ Existing code using `$result['names']` continues to work
- ✅ Invalid pak files fall back to legacy scanner with warning log
- ✅ Existing `$fileInfo->data['pak']` array preserved

## Future Enhancements

### Potential Improvements

1. Support additional object types (39 total in Simutrans)
2. Parse additional metadata fields (intro_date, retire_date, speed, capacity, etc.)
3. Validate metadata against known ranges
4. Generate human-readable compiler version string (e.g., "0.1.3" from 1003)
5. Cache parsed results for performance

### Test Coverage

- ✅ Valid pak file parsing
- ✅ Invalid header rejection
- ✅ Empty file handling
- ✅ Metadata structure validation
- ⚠️ Missing: Generated test files with known metadata (requires makeobj)

## References

- Simutrans source code: `simutrans/` submodule (OTRP-KUTAv6 branch)
- Header format: `simutrans/descriptor/writer/obj_writer.cc`
- Node format: `simutrans/dataobj/loadsave.cc`
- Object types: `simutrans/simtypes.h`

## Conclusion

Complete implementation of Simutrans .pak binary parser with:

- ✅ All 12 components implemented
- ✅ All tests passing (89/89)
- ✅ Backward compatibility maintained
- ✅ Fallback to legacy scanner on errors
- ✅ TypeScript types updated
- ✅ Batch reparse command available
- ✅ Documentation complete

Ready for production use.
