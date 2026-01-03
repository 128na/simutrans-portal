# PAK パーサー実装詳細

Simutrans PAK ファイル形式の解析ロジック、バイナリフォーマット理解、実装アーキテクチャ。

---

## 目次

1. [概要](#概要)
2. [バイナリフォーマット](#バイナリフォーマット)
3. [実装アーキテクチャ](#実装アーキテクチャ)
4. [Extractor](#extractor)
5. [デバッグとテスト](#デバッグとテスト)

---

## 概要

### PAK ファイルの構造

Simutrans PAK ファイルは、複数のゲームアセット（オブジェクト）がバイナリ形式で連続に格納されたファイルです。

```
┌─────────────────┐
│  Object Entry 1 │ (vehicle)
├─────────────────┤
│  Object Entry 2 │ (building)
├─────────────────┤
│  Object Entry 3 │ (terrain)
└─────────────────┘
```

各 Object Entry は以下の構造:

```
┌────────────────────┐
│ Type ID (1 byte)   │
├────────────────────┤
│ Data Size (2 bytes)│
├────────────────────┤
│ Object Data        │
│ (variable length)  │
└────────────────────┘
```

### PAK バージョン

| バージョン | 説明             | リリース           |
| ---------- | ---------------- | ------------------ |
| 80         | 基本フォーマット | Simutrans 0.80     |
| 100        | 拡張フォーマット | Simutrans 1.00     |
| 110        | 現在の標準       | Simutrans 1.10     |
| 121        | Extended対応     | Simutrans Extended |

---

## バイナリフォーマット

### マジックナンバー

最初の 4 バイト: `PAKX` (ASCII)

```php
$magic = fread($file, 4);  // "PAKX"
if ($magic !== 'PAKX') {
    throw new InvalidPakException('Not a valid PAK file');
}
```

### オブジェクトエントリ

#### Type ID（1 バイト）

```
0x00 = 終了マーカー (EOF)
0x01 = Vehicle (乗り物)
0x02 = Building (建物)
0x03 = Tree (樹木)
0x04 = Way (道路)
0x05 = Scenery (風景)
0x06 = Terrain (地形)
0x07 = Tunnel (トンネル)
... (その他のタイプ)
```

#### Data Size（2 バイト、リトルエンディアン）

オブジェクトデータのバイト数を示す16ビット整数。

```php
$sizeBytes = fread($file, 2);
$size = unpack('v', $sizeBytes)[1];  // リトルエンディアン
```

#### Object Data（可変長）

タイプ別の定義に従うバイナリデータ。

---

## 実装アーキテクチャ

### ディレクトリ構造

```
app/Services/FileInfo/
├── FileInfoService.php       # 公開インターフェース
├── Extractors/
│   ├── Extractor.php        # 基底クラス
│   ├── File/
│   │   ├── MakeobjectsExtractor.php
│   │   └── Pak/
│   │       ├── PakExtractor.php
│   │       ├── PakFile.php
│   │       └── ObjectTypes.php
│   └── DatExtractor.php      # .dat ファイル用
└── Exceptions/
    ├── FileException.php
    ├── InvalidPakException.php
    └── ExtractionException.php
```

### FileInfoService（ファサード）

```php
<?php

namespace App\Services\FileInfo;

use Illuminate\Http\UploadedFile;

/**
 * ファイル情報抽出サービス
 */
class FileInfoService
{
    public function __construct(
        private MakeobjectsExtractor $makeobjectsExtractor,
        private PakExtractor $pakExtractor,
        private DatExtractor $datExtractor,
    ) {}

    /**
     * ファイルからメタデータを抽出
     */
    public function extract(UploadedFile|string $file): array
    {
        $path = $file instanceof UploadedFile
            ? $file->getRealPath()
            : $file;

        // ファイル拡張子で Extractor を選択
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return match ($extension) {
            'pak' => $this->pakExtractor->extract($path),
            'zip' => $this->handleZipFile($path),
            'dat' => $this->datExtractor->extract($path),
            default => throw new FileException("Unsupported file type: {$extension}"),
        };
    }

    private function handleZipFile(string $path): array
    {
        // ZIP内の PAK ファイルを処理
        $zip = new ZipArchive();
        $zip->open($path);

        foreach (range(0, $zip->numFiles - 1) as $i) {
            $filename = $zip->getNameIndex($i);
            if (str_ends_with($filename, '.pak')) {
                $tempPath = tempnam(sys_get_temp_dir(), 'pak');
                file_put_contents($tempPath, $zip->getFromIndex($i));

                $result = $this->pakExtractor->extract($tempPath);
                unlink($tempPath);

                return $result;
            }
        }

        throw new FileException('No PAK file found in ZIP');
    }
}
```

---

## Extractor

### 基底クラス

```php
<?php

namespace App\Services\FileInfo\Extractors;

use Illuminate\Contracts\Support\Responsable;

/**
 * ファイル形式の抽象 Extractor
 */
abstract class Extractor
{
    /**
     * ファイルからメタデータを抽出
     *
     * @param string $filePath ファイルパス
     * @return array 抽出されたメタデータ
     */
    abstract public function extract(string $filePath): array;

    /**
     * ファイルが処理可能か判定
     */
    abstract protected function validate(string $filePath): bool;

    /**
     * ファイルを開く
     */
    protected function openFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new FileException("File not found: {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new FileException("File is not readable: {$filePath}");
        }

        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            throw new FileException("Cannot open file: {$filePath}");
        }

        return $handle;
    }

    /**
     * ファイルを閉じる
     */
    protected function closeFile($handle): void
    {
        if (is_resource($handle)) {
            fclose($handle);
        }
    }
}
```

### PAK Extractor

```php
<?php

namespace App\Services\FileInfo\Extractors\File\Pak;

use App\Services\FileInfo\Extractors\Extractor;

/**
 * PAK ファイル形式 Extractor
 */
class PakExtractor extends Extractor
{
    public function __construct(
        private PakFile $pakFile,
        private ObjectTypes $objectTypes,
    ) {}

    /**
     * PAK ファイルを解析
     */
    public function extract(string $filePath): array
    {
        if (!$this->validate($filePath)) {
            throw new InvalidPakException("Invalid PAK file: {$filePath}");
        }

        $file = $this->openFile($filePath);

        try {
            return $this->parsePakFile($file, $filePath);
        } finally {
            $this->closeFile($file);
        }
    }

    protected function validate(string $filePath): bool
    {
        return $this->pakFile->isPakFile($filePath);
    }

    /**
     * PAK ファイルを解析
     */
    private function parsePakFile($handle, string $filePath): array
    {
        $pakVersion = $this->readPakVersion($handle);
        $objects = [];

        while (!feof($handle)) {
            $typeId = ord(fgetc($handle));

            // EOF マーカーで終了
            if ($typeId === 0x00) {
                break;
            }

            // データサイズを読み込み
            $sizeBytes = fread($handle, 2);
            $size = unpack('v', $sizeBytes)[1];

            // オブジェクトデータを読み込み
            $data = fread($handle, $size);

            // オブジェクトを解析
            $object = $this->parseObject($typeId, $data);
            $objects[] = $object;
        }

        return [
            'pak_version' => $pakVersion,
            'content_type' => $this->determineContentType($objects),
            'object_count' => count($objects),
            'objects' => $objects,
        ];
    }

    /**
     * PAK バージョンを読み込み
     */
    private function readPakVersion($handle): string
    {
        // マジックナンバーを読み込み（"PAKX"）
        $magic = fread($handle, 4);
        if ($magic !== 'PAKX') {
            throw new InvalidPakException('Missing magic number PAKX');
        }

        // バージョンを読み込み（4 バイト）
        $versionBytes = fread($handle, 4);
        $version = unpack('V', $versionBytes)[1];

        return (string)$version;
    }

    /**
     * オブジェクトを解析
     */
    private function parseObject(int $typeId, string $data): array
    {
        return match ($typeId) {
            0x01 => $this->objectTypes->parseVehicle($data),
            0x02 => $this->objectTypes->parseBuilding($data),
            0x03 => $this->objectTypes->parseTree($data),
            0x04 => $this->objectTypes->parseWay($data),
            0x05 => $this->objectTypes->parseScenery($data),
            0x06 => $this->objectTypes->parseTerrain($data),
            0x07 => $this->objectTypes->parseTunnel($data),
            default => [
                'type' => 'unknown',
                'type_id' => $typeId,
                'size' => strlen($data),
            ],
        };
    }

    /**
     * コンテンツタイプを判定
     */
    private function determineContentType(array $objects): string
    {
        if (empty($objects)) {
            return 'empty';
        }

        $types = array_count_values(array_column($objects, 'type'));
        $primaryType = array_key_first($types);

        return match ($primaryType) {
            'vehicle' => 'vehicle',
            'building' => 'building',
            'terrain' => 'terrain',
            'scenery' => 'scenery',
            default => 'mixed',
        };
    }
}
```

### ObjectTypes（型別解析）

```php
<?php

namespace App\Services\FileInfo\Extractors\File\Pak;

/**
 * PAK オブジェクトタイプ別の解析
 */
class ObjectTypes
{
    /**
     * Vehicle オブジェクトを解析
     */
    public function parseVehicle(string $data): array
    {
        // バイナリデータをデコード
        $offset = 0;

        // オブジェクト ID（可変長）
        $objectId = $this->readString($data, $offset);

        // その他の属性...
        // (詳細な実装は仕様ドキュメントを参照)

        return [
            'type' => 'vehicle',
            'id' => $objectId,
            'name' => $this->readString($data, $offset),
            'version' => $this->readUInt16($data, $offset),
            'capacity' => $this->readUInt16($data, $offset),
        ];
    }

    /**
     * Building オブジェクトを解析
     */
    public function parseBuilding(string $data): array
    {
        $offset = 0;

        return [
            'type' => 'building',
            'id' => $this->readString($data, $offset),
            'name' => $this->readString($data, $offset),
            'size' => [
                'width' => $this->readUInt8($data, $offset),
                'height' => $this->readUInt8($data, $offset),
            ],
        ];
    }

    /**
     * その他のタイプも同様に実装
     */
    // ...

    // ヘルパーメソッド

    private function readString(string $data, int &$offset): string
    {
        $length = ord($data[$offset++]);
        $string = substr($data, $offset, $length);
        $offset += $length;
        return $string;
    }

    private function readUInt8(string $data, int &$offset): int
    {
        return ord($data[$offset++]);
    }

    private function readUInt16(string $data, int &$offset): int
    {
        $value = unpack('v', substr($data, $offset, 2))[1];
        $offset += 2;
        return $value;
    }

    private function readUInt32(string $data, int &$offset): int
    {
        $value = unpack('V', substr($data, $offset, 4))[1];
        $offset += 4;
        return $value;
    }
}
```

---

## デバッグとテスト

### ユニットテスト

```php
<?php

namespace Tests\Unit\Services\FileInfo\Extractors\File\Pak;

use App\Services\FileInfo\Extractors\File\Pak\PakExtractor;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class PakExtractorTest extends TestCase
{
    private PakExtractor $extractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extractor = app(PakExtractor::class);
    }

    public function test_extracts_valid_pak_file(): void
    {
        $pakFile = storage_path('test-data/vehicles.pak');

        $result = $this->extractor->extract($pakFile);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pak_version', $result);
        $this->assertArrayHasKey('objects', $result);
        $this->assertGreaterThan(0, $result['object_count']);
    }

    public function test_throws_exception_for_invalid_file(): void
    {
        $this->expectException(InvalidPakException::class);
        $this->extractor->extract('invalid.pak');
    }

    public function test_correctly_identifies_content_type(): void
    {
        $pakFile = storage_path('test-data/vehicles.pak');
        $result = $this->extractor->extract($pakFile);

        $this->assertIn($result['content_type'], ['vehicle', 'building', 'mixed']);
    }
}
```

### テストデータ

```bash
# テストデータの格納位置
storage/test-data/
├── vehicles.pak          # 車両 PAK ファイル
├── buildings.pak         # 建物 PAK ファイル
├── invalid.pak           # 無効なファイル
└── makeobjects/          # makeobjects 出力
    └── pak.lst
```

---

## パフォーマンス最適化

### ストリーム処理

```php
// ❌ 非効率: ファイル全体をメモリに読み込み
$content = file_get_contents($filePath);
$handle = fopen('php://memory', 'r+');
fwrite($handle, $content);
rewind($handle);

// ✅ 効率的: ストリーム処理
$handle = fopen($filePath, 'rb');
while (!feof($handle)) {
    // チャンク単位で処理
}
```

### メモリ制限

```php
// 大きなファイルの場合はメモリ制限を設定
ini_set('memory_limit', '512M');

// または非同期ジョブで処理
ProcessPakFile::dispatch($filePath);
```

---

## 関連ドキュメント

- **使用方法**: [PAK Parser Usage](./pak-parser-usage-20260103-knowledge.md)
- **フィールド標準**: [PAK Parser Field Standards](../spec/pak-parser-field-standards-spec.md)
- **バイナリフォーマット**: [PAK Tunnel Format](../spec/pak-tunnel-format-spec.md)

---

**最終更新**: 2025-11-24  
**バージョン**: 1.0.0
