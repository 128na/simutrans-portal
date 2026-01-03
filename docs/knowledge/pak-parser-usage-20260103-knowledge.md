# PAK パーサー使用方法

Simutrans のアドオン PAK ファイルからメタデータを抽出するための使用ガイド。

---

## 目次

1. [概要](#概要)
2. [セットアップ](#セットアップ)
3. [基本的な使用方法](#基本的な使用方法)
4. [メタデータの種類](#メタデータの種類)
5. [トラブルシューティング](#トラブルシューティング)
6. [API リファレンス](#apiリファレンス)

---

## 概要

### PAK ファイルとは

Simutrans のアドオン形式である `.pak` ファイルは、バイナリ形式で複数のゲームアセット（車両・建物・地形等）を含む複合ファイルです。

### このツールの目的

PAK ファイルを解析して、以下のメタデータを自動抽出します:

- 車両・建物・地形等のオブジェクト情報
- バージョン・制作者等の属性情報
- 要件・互換性情報

---

## セットアップ

### インストール

```bash
composer install
```

### 依存ライブラリ

- PHP 8.3+
- Laravel 12
- Binary-safe file I/O

---

## 基本的な使用方法

### コントローラーでの使用

```php
<?php

use App\Services\FileInfo\FileInfoService;
use Illuminate\Http\Request;

class AttachmentController
{
    public function __construct(
        private FileInfoService $fileInfoService,
    ) {}

    /**
     * PAK ファイルをアップロード
     */
    public function store(Request $request)
    {
        $file = $request->file('file');

        // PAK ファイルのメタデータを抽出
        $fileInfo = $this->fileInfoService->extract($file);

        return response()->json([
            'id' => $file->id,
            'metadata' => $fileInfo,
        ]);
    }
}
```

### Model での使用

```php
<?php

use App\Models\Attachment;

class Attachment extends Model
{
    public function fileInfo()
    {
        return $this->hasOne(FileInfo::class);
    }
}

// 使用例
$attachment = Attachment::find(1);
$metadata = $attachment->fileInfo;

echo $metadata->pak_version;      // "121"
echo $metadata->content_type;     // "vehicle"
echo $metadata->object_count;     // 42
```

### Job での使用（非同期処理）

```php
<?php

use App\Jobs\Attachments\JobGenerateThumbnail;
use App\Services\FileInfo\FileInfoService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class ProcessPakFile implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        private int $attachmentId,
    ) {}

    public function handle(FileInfoService $fileInfoService)
    {
        $attachment = Attachment::find($this->attachmentId);

        // PAK 解析
        $fileInfo = $fileInfoService->extract($attachment->file);

        // 結果を DB に保存
        $attachment->fileInfo()->create([
            'pak_version' => $fileInfo->pak_version,
            'content_type' => $fileInfo->content_type,
            'metadata' => json_encode($fileInfo->metadata),
        ]);

        // サムネイル生成（オプション）
        JobGenerateThumbnail::dispatch($attachment);
    }
}
```

---

## メタデータの種類

### 抽出される主要情報

```json
{
  "pak_version": "121",
  "content_type": "vehicle",
  "object_count": 42,
  "objects": [
    {
      "id": "DB-18",
      "name": "German Suburban Train",
      "type": "rail_vehicle",
      "capacity": 120,
      "version": "1.0",
      "preview": "byte_offset:1024,width:64,height:32"
    }
  ],
  "metadata": {
    "creation_date": "2020-01-01",
    "creator": "Simutrans Community",
    "pak_file": "pak128.zip",
    "description": "Deutsche Bahn addon for Simutrans Extended"
  }
}
```

### コンテンツタイプ

| Type       | 説明         | 例                         |
| ---------- | ------------ | -------------------------- |
| `vehicle`  | 車両アセット | 電車・バス・飛行機         |
| `building` | 建物・建造物 | 駅舎・工場・住宅           |
| `terrain`  | 地形・道路   | 芝生・砂利道・橋           |
| `scenery`  | 風景・装飾   | 木・看板・イルミネーション |
| `mixed`    | 複合         | 複数タイプを含む           |

---

## コマンドラインでの使用

### Artisan コマンド（予定）

```bash
# PAK ファイルを解析
php artisan pak:analyze path/to/file.pak

# JSON 形式で出力
php artisan pak:analyze path/to/file.pak --json

# 詳細情報を表示
php artisan pak:analyze path/to/file.pak --verbose
```

---

## トラブルシューティング

### エラー: "Could not open PAK file"

```php
// ❌ 失敗
$fileInfo = $fileInfoService->extract('path/to/file.pak');

// ✅ 成功
$file = request()->file('pak_file');
$fileInfo = $fileInfoService->extract($file);
```

**原因**: ファイルパスではなく、UploadedFile インスタンスを渡す必要があります。

### 警告: "Unknown PAK version"

一部の古い または実験的な PAK バージョンはサポートされていない可能性があります。

**対応**:

- PAK ファイルを最新バージョンで再パッケージ化
- 互換性ドキュメント: [PAK Tunnel Format](./pak-tunnel-format-20260103-knowledge.md)

### メモリ不足エラー

大きな PAK ファイル（100MB以上）を処理する場合:

```php
// 非同期ジョブで処理
ProcessPakFile::dispatch($attachment->id);
```

または php.ini で memory_limit を増やす:

```ini
memory_limit = 512M
```

---

## API リファレンス

### FileInfoService

```php
namespace App\Services\FileInfo;

class FileInfoService
{
    /**
     * PAK ファイルを解析
     *
     * @param UploadedFile|string $file PAK ファイル
     * @return array 抽出されたメタデータ
     * @throws FileException
     */
    public function extract(UploadedFile|string $file): array;

    /**
     * マジックナンバーで PAK ファイルか判定
     *
     * @param string $filePath ファイルパス
     * @return bool
     */
    public function isPakFile(string $filePath): bool;

    /**
     * PAK バージョンを取得
     *
     * @param string $filePath ファイルパス
     * @return string|null バージョン（例: "121"）
     */
    public function getPakVersion(string $filePath): ?string;
}
```

### FileInfo Model

```php
namespace App\Models\Attachment;

class FileInfo extends Model
{
    protected $fillable = [
        'attachment_id',
        'pak_version',
        'content_type',
        'object_count',
        'metadata',
        'error_message',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }
}
```

---

## 実装例

### フロントエンド: ファイルアップロード

```typescript
// resources/js/features/attachments/Upload.tsx

import { useState } from 'react';
import axios from 'axios';

const Upload = () => {
  const [loading, setLoading] = useState(false);
  const [metadata, setMetadata] = useState(null);

  const handleUpload = async (file: File) => {
    setLoading(true);

    const formData = new FormData();
    formData.append('file', file);

    try {
      const response = await axios.post('/api/v2/attachments', formData);
      setMetadata(response.data.metadata);
    } catch (error) {
      console.error('Upload failed:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <input
        type="file"
        accept=".pak"
        onChange={(e) => handleUpload(e.target.files?.[0]!)}
        disabled={loading}
      />
      {metadata && <pre>{JSON.stringify(metadata, null, 2)}</pre>}
    </div>
  );
};

export default Upload;
```

### バックエンド: REST API

```php
// routes/api.php
Route::post('/v2/attachments', AttachmentController::class);

// app/Http/Controllers/AttachmentController.php
class AttachmentController
{
    public function __invoke(
        Request $request,
        FileInfoService $fileInfoService,
    ): JsonResponse {
        $file = $request->file('file');

        // PAK 解析
        $metadata = $fileInfoService->extract($file);

        // 添付ファイル作成
        $attachment = $request->user()->attachments()->create([
            'file' => $file->store('attachments'),
            'type' => $metadata['content_type'],
        ]);

        // メタデータ保存
        $attachment->fileInfo()->create([
            'pak_version' => $metadata['pak_version'],
            'object_count' => count($metadata['objects']),
            'metadata' => $metadata,
        ]);

        return response()->json([
            'id' => $attachment->id,
            'metadata' => $metadata,
        ]);
    }
}
```

---

## パフォーマンス最適化

### 大量ファイル処理

```php
// ❌ 非効率: 同期処理
foreach ($files as $file) {
    $fileInfoService->extract($file);  // ブロック
}

// ✅ 効率的: 非同期キュー
foreach ($files as $file) {
    ProcessPakFile::dispatch($file->id);  // ノンブロック
}
```

### キャッシング

```php
use Illuminate\Support\Facades\Cache;

$metadata = Cache::remember(
    "pak:{$file->hash()}",
    now()->addDays(30),
    fn() => $fileInfoService->extract($file),
);
```

---

## 関連ドキュメント

- **実装詳細**: [PAK Parser Implementation](./pak-parser-implementation-20260103-knowledge.md)
- **フィールド標準**: [PAK Parser Field Standards](../spec/pak-parser-field-standards-spec.md)
- **バイナリフォーマット**: [PAK Tunnel Format](../spec/pak-tunnel-format-spec.md)

---

**最終更新**: 2025-11-24  
**バージョン**: 1.0.0
