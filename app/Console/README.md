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
│   ├── Attachment/
│   │   └── GenerateThumbnailsCommand.php # サムネイル一括生成
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

### Attachment/GenerateThumbnailsCommand

アップロード済み画像のサムネイルを一括生成します。

```bash
php artisan attachment:generate-thumbnails
```

**オプション**:

- `--force`: サムネイルが既に存在する場合も再生成
- `--limit=N`: 処理する件数の上限を指定
- `--sync`: キューを使わず同期実行

**機能**:

- 画像タイプの添付ファイルを取得
- サムネイルが未生成のものを対象（`--force`で全件）
- `JobGenerateThumbnail`をキューにディスパッチ（`--sync`で同期実行）
- プログレスバーで進捗表示

**使用例**:

```bash
# サムネイル未生成の画像を処理
php artisan attachment:generate-thumbnails

# 全画像を再生成（上限100件）
php artisan attachment:generate-thumbnails --force --limit=100

# 同期実行（キューワーカー不要）
php artisan attachment:generate-thumbnails --sync
```

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

### 本番のキャッシュストア切り替え

本番では Web と cron が同じホスト上の同じアプリを使う。既定キャッシュを
`file` に切り替えると、スケジュールの一時停止と重複防止ロックは
`storage/framework/cache/data` に保存される。セッションは `file`、
キューは `database` のまま運用する。レート制限は並行更新時のカウンターを
保つため `CACHE_LIMITER_STORE=database` で従来のストアを使う。

1. この手順に対応したコードを先に反映し、cron と同じ PHP で
   `config:show cache.default`、`config:show cache.limiter` を確認する。
   Web と CLI が同じキャッシュパスに書き込めることを確認する。
   各実行経路で一意な短寿命キーを相互に読み取り、同じキーのロックを
   片方が保持している間は他方で取得できず、解放後に取得できることを確認する。
   確認用の値とロックだけを削除する。
2. ポータルの cron を止め、新規の手動実行も止める。実行中の
   `schedule:run`、バックグラウンドの `backup:sync-uploads` とその子プロセス、
   `schedule:finish`、キューワーカーなどが終了したことを確認する。
   手動のバックアップ同期は通常運用でも定期実行と重ねない。
   Web をメンテナンス状態にして既存リクエストの終了を待ち、二要素認証の
   使用済みコードが失効してから切り替える。現行本番の受入範囲では終了後2分を取る。
3. 現行ストアから切り替え先へ、停止状態とバックアップの稼働情報を引き継ぐ。
   現行ストアでキーが失効済みまたは存在しない場合は、切り替え先に残っている
   同じキーだけを削除する。
   - スケジュールの `illuminate:schedule:paused` と、設定済みのキュー停止キー
     `illuminate:queues:paused`、`illuminate:queue:paused:<connection>:<queue>`
   - `backup-sync-uploads-health:{dropbox|local_backup}:{first-observed|last-complete|alerted}`
   - `backup-sync-uploads-digest:{transferred|errors}:{YYYY-MM-DD}` の有効な日付分
   - `alerted` と集計値は残りの有効期間を引き継ぐ。実行中の処理がないことを
     確認してから、切り替え先に残ったスケジュールロックだけを必要に応じて解除する。
4. 本番環境で `CACHE_DRIVER=file` と `CACHE_LIMITER_STORE=database` を設定する。
   `SCHEDULE_CACHE_DRIVER` または `SCHEDULE_CACHE_STORE` の上書きがないことを確認し、
   cron と同じ PHP で `config:cache` を実行する。Web と CLI の実効設定と停止状態を確認する。
   メンテナンス状態を解除し、cron を再開して実行ログとバックアップの集計を確認する。

切り戻しでも Web と cron と手動実行を同じ手順で停止し、停止状態と稼働情報を
現在のストアから戻し先に同期する。`CACHE_DRIVER=database` に戻して
設定キャッシュを再生成し、実効設定を確認して Web と cron を再開する。
通常のデプロイではアプリのキャッシュを保持する。切り替えや切り戻しでも
`cache:clear` による一括削除は行わない。

### 新着・お知らせのスナップショット

`article:refresh-public-snapshot` は10分ごとに実行し、公開中の新着と
お知らせ、主要3 pak（128-japan・128・64）× 種類ごとの公開記事数を
`storage/app/public-article-snapshot.json` に一括保存する。
件数はトップの「種類から探す」と pak 別一覧の種類の切り替えに表示し、閲覧時に集計しない。
件数が無い旧形式（version 1）や件数だけが壊れたデータでは、新着とお知らせを表示し、件数を省略する。
新着カードは一覧ページと同じカード（`ArticleCard.tsx`）で表示するため、投稿形式（`post_type`）、
pak・種類のカテゴリ（`categories`）、作者名と表示名（`user`）も保存する。
ダウンロード・掲載先・作者の URL は表示時に保存した ID から組み立て、一覧と同じ CV 経路を使う。
記事の pak の slug（`paks`）は、旧コードへ戻したときに pak のタグを出せるよう残す。
これらは version 2 のまま追加した任意項目で、無い・壊れているカードは入手の操作・タグ・作者だけを省略する。
Web と CLI は同じパスと実行ユーザーを使用する。生成ログで成功時刻と失敗を確認できる。
通常の公開・予約公開、タイトル・URL・画像・pak・種類・作者名の変更は次回成功時に反映する。
生成失敗時は前回成功結果を最大24時間使う。未生成、破損、期限切れの場合は
一覧の内容を省略してリンクを表示する。

非公開化・削除・退会では、DB 更新前にスナップショットを失効させる。
同時に生成していた古い結果は破棄し、次回成功までリンク表示に切り替える。
読み取り時の新着とお知らせのための DB 接続は不要。
ページの認証や、リンク先の記事・一覧には DB が必要な場合がある。

### 一覧の人気順

`article:refresh-article-popularities` は毎日 4:30 に実行し（`withoutOverlapping(10)`）、
前日までの直近 90 日の CV（ダウンロードと掲載先クリック）を記事ごとに合計して、
順位表 `article_popularities`（記事ごとに 1 行。`article_id` 主キー、`rank` 一意、`conversion_count`）を作り直す。
人気は記事本体の情報ではないので `articles` には持たない。一覧の「人気順」（`?sort=popular`）は
この表を結合して `rank` の昇順に並べ、表示時に `conversion_counts` を集計・結合しない。

- 集計するのは日次行（`type=1`、`period` が `Ymd`）だけ。日付はアプリのタイムゾーンで決め、当日分は含めない。
- 削除済み・非公開も含めて全記事に行を作る（期間内に CV が無い記事は 0）。順位は CV 合計・公開日時・id の降順。
  公開状態は一覧の表示時に絞る。記事を物理削除すると行も消える（外部キーの cascade）。
- ロックしない読み取りで合計してから、1 つのトランザクションで全行を消して 500 件ずつ入れ直す。
  失敗したら取り消して前回の順位を残し、`FAILURE` で終える。
- 最後の作り直しより後に作られた記事は、次の作り直しまで人気順の一覧に出ない（ほかの並び順には出る）。
- 出力に期間・CV のある記事数・行数・所要時間を出す。

表を作るマイグレーションは、`conversion_counts` を読まずに全記事へ仮の順位（CV 0、公開が新しい順）を入れる。
デプロイ直後の人気順は公開が新しい順と同じ並びになる。デプロイ後にこのコマンドを一度手動で実行すると、
すぐに実際の人気順になる（実行しなくても次の 4:30 に入る）。何度実行しても結果は同じ。

```bash
php artisan article:refresh-article-popularities
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
