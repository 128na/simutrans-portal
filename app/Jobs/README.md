# Jobs ディレクトリ

キュージョブ（非同期処理）を配置します。

## ディレクトリ構造

```
Jobs/
├── Article/
│   ├── CheckDeadLinkJob.php          # デッドリンクチェック
│   ├── JobConvertArticle.php         # pak変換
│   └── JobUpdateSearchIndex.php      # 検索インデックス更新
└── Attachments/
    ├── JobConvertWebp.php            # WebP変換
    └── JobGenerateThumbnail.php      # サムネイル生成
```

## キュージョブとは

非同期で実行される処理タスクです。

### 用途

- 画像変換・リサイズ
- メール送信
- 外部API呼び出し
- 大量データ処理
- 長時間実行処理

### メリット

- レスポンスタイムの短縮
- 並列処理可能
- 失敗時のリトライ
- 実行状況の追跡

## 主要ジョブ

### Article/CheckDeadLinkJob

記事内のデッドリンクをチェックします。

```php
CheckDeadLinkJob::dispatch($article);
```

**処理内容**:

1. 記事内のURLを抽出
2. HTTPリクエストでステータスコード確認
3. 404等のエラーをDB記録

**リトライ**: 3回

**タイムアウト**: 120秒

### Article/JobConvertArticle

pak変換を実行します。

```php
JobConvertArticle::dispatch($article);
```

**処理内容**:

1. `.dat`ファイルをダウンロード
2. `makeobj` コマンドで `.pak` に変換
3. 変換済みファイルをストレージ保存
4. 添付ファイルとして登録

**リトライ**: なし（失敗時は手動再実行）

**タイムアウト**: 300秒

### Article/JobUpdateSearchIndex

検索インデックスを更新します。

```php
JobUpdateSearchIndex::dispatch($article);
```

**処理内容**:

1. 記事のタイトル・本文を解析
2. 形態素解析（オプション）
3. 検索インデックステーブルに保存

**リトライ**: 2回

**タイムアウト**: 60秒

### Attachments/JobConvertWebp

画像をWebP形式に変換します。

```php
JobConvertWebp::dispatch($attachment);
```

**処理内容**:

1. 元画像を取得
2. WebPに変換（品質80%）
3. ストレージに保存
4. 元画像と置き換え（オプション）

**リトライ**: 3回

**タイムアウト**: 180秒

### Attachments/JobGenerateThumbnail

サムネイル画像を生成します。

```php
JobGenerateThumbnail::dispatch($attachment);
```

**処理内容**:

1. 元画像を取得
2. 指定サイズにリサイズ（300x300）
3. サムネイルをストレージに保存
4. Attachmentレコードを更新

**リトライ**: 3回

**タイムアウト**: 60秒

## 実装パターン

### 基本構造

```php
<?php

declare(strict_types=1);

namespace App\Jobs\Article;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExampleJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * リトライ回数
     */
    public int $tries = 3;

    /**
     * タイムアウト（秒）
     */
    public int $timeout = 120;

    /**
     * コンストラクタ
     */
    public function __construct(
        public Article $article,
    ) {}

    /**
     * ジョブを実行
     */
    public function handle(SomeAction $action): void
    {
        ($action)($this->article);
    }
}
```

### ディスパッチ

```php
// 即座にキューに投入
ExampleJob::dispatch($article);

// 遅延実行（10分後）
ExampleJob::dispatch($article)->delay(now()->addMinutes(10));

// 特定のキューに投入
ExampleJob::dispatch($article)->onQueue('high');

// チェーン実行
JobConvertArticle::withChain([
    new JobGenerateThumbnail($attachment),
    new JobConvertWebp($attachment),
])->dispatch($article);
```

### 失敗時の処理

```php
/**
 * ジョブ失敗時の処理
 */
public function failed(?Throwable $exception): void
{
    // ログ記録
    Log::error('Job failed', [
        'article_id' => $this->article->id,
        'exception' => $exception?->getMessage(),
    ]);

    // 通知
    $this->article->user->notify(new JobFailedNotification($this->article));
}
```

### リトライ条件

```php
/**
 * リトライするか判定
 */
public function retryUntil(): DateTime
{
    return now()->addHours(24);
}

/**
 * 特定の例外のみリトライ
 */
public function shouldRetry(Throwable $exception): bool
{
    return $exception instanceof TimeoutException;
}
```

## キュー設定

### .env

```env
QUEUE_CONNECTION=database

# Redis使用時
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### config/queue.php

```php
'connections' => [
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
    ],

    'redis' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],
```

## キューワーカー

### 起動

```bash
# デフォルトキュー
php artisan queue:work

# 特定のキュー
php artisan queue:work --queue=high,default

# 1ジョブのみ実行
php artisan queue:work --once

# タイムアウト設定
php artisan queue:work --timeout=60
```

### Supervisor設定

本番環境では Supervisor でキューワーカーを常駐させます。

```ini
[program:simutrans-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/simutrans-portal/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/simutrans-portal/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Supervisor再起動
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start simutrans-worker:*
```

## モニタリング

### キューの状態確認

```bash
# キュー内のジョブ数
php artisan queue:monitor

# 失敗したジョブ一覧
php artisan queue:failed

# 失敗したジョブを再実行
php artisan queue:retry {id}

# すべての失敗ジョブを再実行
php artisan queue:retry all

# 失敗したジョブを削除
php artisan queue:forget {id}

# すべての失敗ジョブを削除
php artisan queue:flush
```

### Laravel Horizon（Redis使用時）

```bash
composer require laravel/horizon

php artisan horizon:install
php artisan horizon
```

Webダッシュボード: `http://localhost:8000/horizon`

## テスト

ジョブのテストは `tests/Unit/Jobs/` に配置します。

```php
use Illuminate\Support\Facades\Queue;

final class CheckDeadLinkJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_is_dispatched(): void
    {
        Queue::fake();

        $article = Article::factory()->create();

        CheckDeadLinkJob::dispatch($article);

        Queue::assertPushed(CheckDeadLinkJob::class, function ($job) use ($article) {
            return $job->article->id === $article->id;
        });
    }

    public function test_job_handles_correctly(): void
    {
        $article = Article::factory()->create([
            'contents' => [
                ['type' => 'text', 'text' => 'https://example.com'],
            ],
        ]);

        $job = new CheckDeadLinkJob($article);
        $job->handle(app(CheckDeadLink::class));

        $this->assertDatabaseHas('article_link_check_histories', [
            'article_id' => $article->id,
        ]);
    }
}
```

## ベストプラクティス

### ✅ 推奨

- ジョブは小さく単一責任
- ビジネスロジックはActionに委譲
- `SerializesModels` でモデルをシリアライズ
- `timeout` と `tries` を適切に設定
- `failed()` メソッドでエラーハンドリング

### ❌ 避けるべき

- 大きすぎるジョブ（分割する）
- モデルの全カラムをコンストラクタに渡す（モデル全体を渡す）
- 外部依存の直接呼び出し（Serviceに委譲）
- 無限リトライ

### コマンド vs Job

| 項目         | コマンド             | Job            |
| ------------ | -------------------- | -------------- |
| **実行方法** | `php artisan` / Cron | キューワーカー |
| **並列処理** | 不可                 | 可能           |
| **失敗時**   | エラーログのみ       | リトライ可能   |
| **実行時間** | 短時間推奨           | 長時間OK       |
| **進捗表示** | プログレスバー       | なし           |
| **例**       | 一括インデックス更新 | 1記事ずつ処理  |

## 関連ドキュメント

- **[Commands](../Console/README.md)** - Artisanコマンド
- **[Actions](../Actions/README.md)** - ビジネスロジック
- **[Events](../Events/README.md)** - イベント駆動
- **[README.md](../../README.md)** - プロジェクト概要
