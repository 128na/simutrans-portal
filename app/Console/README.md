# Console Commands ディレクトリ

Artisanカスタムコマンドを配置します。

## ディレクトリ構造

```
Console/
├── Commands/
│   ├── Article/
│   │   ├── CheckDeadLinkCommand.php      # デッドリンクチェック
│   │   ├── ConvertCommand.php            # pak変換
│   │   └── UpdateSearchIndexCommand.php  # 検索インデックス更新
│   ├── LangJsonExportCommand.php         # 翻訳JSONエクスポート
│   ├── MFASetupAutoRecovery.php          # 2FA自動リカバリ
│   └── RemoveUnusedTagsCommand.php       # 未使用タグ削除
└── Kernel.php                            # コマンドカーネル
```

## コマンド一覧

### Article/CheckDeadLinkCommand

デッドリンクをチェックします。

```bash
php artisan article:check-dead-links
```

**機能**:

- 記事内のURLをチェック
- HTTPステータスコードを確認
- 404等のエラーをDB記録
- デッドリンク件数をカウント

**スケジュール**: 毎日実行（`console.php`）

**実装**:

```php
protected $signature = 'article:check-dead-links';
protected $description = '記事内のリンク切れをチェックします';

public function handle(CheckDeadLink $checkDeadLink): int
{
    $this->info('デッドリンクチェックを開始します...');

    Article::cursor()->each(function (Article $article) use ($checkDeadLink) {
        ($checkDeadLink)($article);
    });

    $this->info('完了しました');
    return self::SUCCESS;
}
```

### Article/ConvertCommand

pak変換を実行します（Simutrans特有の処理）。

```bash
php artisan article:convert {article}
```

**機能**:

- `.dat`ファイル → `.pak`ファイルへ変換
- makeobj コマンド実行
- 変換結果を添付ファイルとして保存

### Article/UpdateSearchIndexCommand

記事の検索インデックスを更新します。

```bash
php artisan article:update-search-index
```

**機能**:

- 全記事のタイトル・本文を解析
- 検索用インデックステーブルに保存
- 全文検索のパフォーマンス向上

**スケジュール**: 1時間毎に実行

### LangJsonExportCommand

翻訳JSON（`lang/ja.json`）をエクスポートします。

```bash
php artisan lang:export
```

**機能**:

- Bladeテンプレート内の `__()` 関数をスキャン
- 翻訳キーを抽出
- `lang/ja.json` に出力

**用途**: 翻訳の漏れをチェック

### MFASetupAutoRecovery

2FA（多要素認証）の自動リカバリを設定します。

```bash
php artisan mfa:setup-auto-recovery
```

**機能**:

- 2FA設定済みユーザーにリカバリコードを生成
- メールで通知
- セキュリティ強化

### RemoveUnusedTagsCommand

未使用タグを削除します。

```bash
php artisan tags:remove-unused
```

**機能**:

- 記事に紐づいていないタグを検出
- 確認後に削除
- タグデータの整理

## コマンド実装パターン

### 基本構造

```php
<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExampleCommand extends Command
{
    protected $signature = 'example:command {argument} {--option=}';
    protected $description = 'コマンドの説明';

    public function handle(): int
    {
        $this->info('処理を開始します');

        // 処理

        $this->info('完了しました');
        return self::SUCCESS;
    }
}
```

### 引数とオプション

```php
// 必須引数
protected $signature = 'article:convert {article}';

// オプション引数
protected $signature = 'article:convert {article?}';

// オプション（デフォルト値付き）
protected $signature = 'article:check {--force} {--limit=10}';

// 配列引数
protected $signature = 'article:convert {articles*}';

// 説明付き
protected $signature = 'article:convert
    {article : The ID of the article}
    {--force : Force the operation}';
```

### 入力の取得

```php
public function handle(): int
{
    $articleId = $this->argument('article');
    $force = $this->option('force');
    $limit = $this->option('limit');

    // ...
}
```

### 出力

```php
// 通常メッセージ
$this->info('情報メッセージ');

// エラーメッセージ
$this->error('エラーメッセージ');

// 警告メッセージ
$this->warn('警告メッセージ');

// 成功メッセージ
$this->comment('コメント');

// プログレスバー
$bar = $this->output->createProgressBar(count($items));
$bar->start();

foreach ($items as $item) {
    // 処理
    $bar->advance();
}

$bar->finish();
```

### 確認プロンプト

```php
if ($this->confirm('本当に削除しますか?')) {
    // 削除処理
}

// デフォルトでYes
if ($this->confirm('続行しますか?', true)) {
    // 処理
}
```

### テーブル表示

```php
$this->table(
    ['ID', 'Title', 'Status'],
    $articles->map(fn($article) => [
        $article->id,
        $article->title,
        $article->status->value,
    ])
);
```

## スケジュール実行

`routes/console.php` でスケジュール登録します。

```php
use Illuminate\Support\Facades\Schedule;

// 毎日午前2時に実行
Schedule::command('article:check-dead-links')->dailyAt('02:00');

// 1時間毎に実行
Schedule::command('article:update-search-index')->hourly();

// 毎週月曜日に実行
Schedule::command('tags:remove-unused')->weekly();

// 毎月1日に実行
Schedule::command('backup:clean')->monthly();
```

### cron設定

サーバーのcrontabに追加:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### スケジュール確認

```bash
# 登録されているスケジュール一覧
php artisan schedule:list

# 次回実行時刻を確認
php artisan schedule:work
```

## テスト

コマンドのテストは `tests/Feature/Console/` に配置します。

```php
use Illuminate\Support\Facades\Artisan;

class CheckDeadLinkCommandTest extends TestCase
{
    public function test_command_runs_successfully(): void
    {
        $article = Article::factory()->create([
            'contents' => [
                ['type' => 'text', 'text' => 'https://example.com'],
            ],
        ]);

        $exitCode = Artisan::call('article:check-dead-links');

        $this->assertEquals(0, $exitCode);
        $this->assertDatabaseHas('article_link_check_histories', [
            'article_id' => $article->id,
        ]);
    }
}
```

## キューとの連携

長時間実行するコマンドはキューにディスパッチします。

```php
public function handle(): int
{
    Article::cursor()->each(function (Article $article) {
        CheckDeadLinkJob::dispatch($article);
    });

    $this->info('ジョブをキューに投入しました');
    return self::SUCCESS;
}
```

```bash
# キューワーカー起動
php artisan queue:work

# 特定のキューのみ
php artisan queue:work --queue=high,default
```

## ベストプラクティス

### ✅ 推奨

- コマンド名は `名詞:動詞` 形式（`article:check`, `tags:remove`）
- `handle()` の戻り値は `self::SUCCESS` または `self::FAILURE`
- 処理の進捗を表示（`$this->info()`, プログレスバー）
- 長時間実行はキューを使用
- スケジュール実行はログを記録

### ❌ 避けるべき

- ビジネスロジックをコマンドに記述（Actionに委譲）
- 確認なしでデータ削除
- エラーハンドリングなし
- 無限ループ

### コマンド vs Job

| 用途         | コマンド         | Job          |
| ------------ | ---------------- | ------------ |
| **実行方法** | `php artisan`    | キュー       |
| **実行時間** | 短時間           | 長時間OK     |
| **並列処理** | 不可             | 可能         |
| **失敗時**   | エラーログ       | リトライ可能 |
| **例**       | インデックス更新 | 画像変換     |

## 関連ドキュメント

- **[Jobs](../Jobs/README.md)** - キュージョブ
- **[Actions](../Actions/README.md)** - ビジネスロジック
- **[README.md](../../README.md)** - プロジェクト概要
