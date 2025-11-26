# Pak Parser Usage Guide

## Quick Start

The Pak Parser is automatically used when uploading .pak files to articles. No additional setup required.

## Accessing Pak Metadata

### Backend (PHP)

```php
use App\Models\FileInfo;

$fileInfo = FileInfo::find($id);

// Get pak metadata
$paksMetadata = $fileInfo->data['paks_metadata'] ?? [];

foreach ($paksMetadata as $filename => $metadataArray) {
    foreach ($metadataArray as $metadata) {
        echo "Name: {$metadata['name']}\n";
        echo "Copyright: {$metadata['copyright']}\n";
        echo "Type: {$metadata['objectType']}\n";
        echo "Compiler: {$metadata['compilerVersionCode']}\n";
    }
}

// Legacy names array (backward compatibility)
$pakNames = $fileInfo->data['pak'] ?? [];
```

### Frontend (TypeScript/React)

```typescript
import type { FileInfo, PakMetadata } from '@/types/models';

function PakMetadataDisplay({ fileInfo }: { fileInfo: FileInfo }) {
  const paksMetadata = fileInfo.data.paks_metadata;

  if (!paksMetadata) {
    return <div>No pak metadata available</div>;
  }

  return (
    <div>
      {Object.entries(paksMetadata).map(([filename, metadataArray]) => (
        <div key={filename}>
          <h3>{filename}</h3>
          {metadataArray.map((metadata: PakMetadata, index) => (
            <div key={index}>
              <p><strong>Name:</strong> {metadata.name}</p>
              <p><strong>Type:</strong> {metadata.objectType}</p>
              {metadata.copyright && (
                <p><strong>Copyright:</strong> {metadata.copyright}</p>
              )}
              <p><strong>Compiler Version:</strong> {metadata.compilerVersionCode}</p>
            </div>
          ))}
        </div>
      ))}
    </div>
  );
}
```

## Batch Reparse Existing Files

If you have existing .pak files uploaded before this implementation, you can reparse them to extract metadata:

```bash
# Dry run (show files without reparsing)
php artisan article:reparse-pak-files --dry-run

# Reparse first 10 files
php artisan article:reparse-pak-files --limit=10

# Reparse all files
php artisan article:reparse-pak-files
```

**Note**: This command will:

- Find all FileInfo records containing .pak files
- Re-extract metadata using the new parser
- Update the `data->paks_metadata` field
- Show progress bar and summary

## Understanding the Metadata

### Fields

- **name** (string): The addon's internal name (e.g., "transparent_vehicle")
- **copyright** (string|null): Author/copyright information (e.g., "128Na")
- **objectType** (string): Type of addon:
  - `vehicle` - Vehicle (road, rail, air, water)
  - `building` - Building (residential, commercial, etc.)
  - `bridge` - Bridge
  - `way` - Way (road, rail, etc.)
  - `tree` - Tree/vegetation
  - `good` - Good/cargo type
  - `unknown_*` - Other types
- **compilerVersionCode** (int): Simutrans compiler version code (e.g., 1003 = version 0.1.3)

### Compiler Version Code Format

The version code is a 4-digit integer where:

- First 2 digits: Major.minor version
- Last 2 digits: Patch version

Examples:

- `1003` = Simutrans 0.1.3
- `1205` = Simutrans 0.1.205 (extended version)

## Fallback Behavior

If the new parser fails (corrupted file, unsupported format, etc.), the system automatically falls back to the legacy parser:

1. Try new parser (extracts full metadata)
2. If exception thrown → use legacy parser (extracts names only)
3. Log warning for investigation
4. Return result with metadata array empty

This ensures backward compatibility and graceful degradation.

## Troubleshooting

### Metadata Not Showing

**Problem**: Uploaded .pak file but no metadata appears

**Solutions**:

1. Check if file is actually a valid .pak file
2. Check Laravel logs for parser warnings
3. Try manual reparse: `php artisan article:reparse-pak-files --limit=1`
4. Verify file is not corrupted

### Incorrect Metadata

**Problem**: Extracted metadata doesn't match expected values

**Solutions**:

1. Verify .pak file is compiled correctly with makeobj
2. Check if file contains TEXT nodes (name/copyright)
3. Some pak files may not have copyright information (will be null)
4. Object type might be `unknown_*` if not in supported list

### Performance Issues

**Problem**: Parsing takes too long

**Solutions**:

1. Parser has built-in safeguards (max depth 100, timeout handling)
2. For batch reparse, use `--limit` option to process in chunks
3. Consider running reparse in background job (already implemented)

## Advanced Usage

### Custom Metadata Extraction

If you need to extract additional fields beyond the default metadata, you can create a custom extractor:

```php
use App\Services\FileInfo\Extractors\Pak\PakParser;
use App\Services\FileInfo\Extractors\Pak\Node;

$parser = app(PakParser::class);
$binary = file_get_contents('addon.pak');
$result = $parser->parse($binary);

// Custom processing
foreach ($result['metadata'] as $metadata) {
    // Access raw node data if needed
    // Extract additional fields
    // Custom validation
}
```

### Testing Parser Changes

```bash
# Run pak parser tests only
php artisan test --filter=PakParserTest

# Run all extractor tests
php artisan test --filter=ExtractorTest

# Full unit test suite
php artisan test --testsuite=Unit
```

## API Endpoints (Future)

Currently, pak metadata is stored in FileInfo and accessible through existing article/attachment APIs. No dedicated pak metadata endpoints yet.

If needed in the future, consider:

- `GET /api/v2/attachments/{id}/pak-metadata` - Get metadata for specific attachment
- `GET /api/v2/articles/{id}/pak-metadata` - Get all pak metadata for article
- `POST /api/v2/pak-metadata/reparse` - Trigger reparse for specific files

## Related Documentation

- **Implementation Details**: [docs/pak-parser-implementation.md](pak-parser-implementation.md)
- **Services and Actions Architecture**: [docs/architecture-services-and-actions.md](architecture-services-and-actions.md)
- **TypeScript Types**: [resources/js/types/models/FileInfo.ts](../resources/js/types/models/FileInfo.ts)
- **Repositories**: [app/Repositories/README.md](../app/Repositories/README.md)
