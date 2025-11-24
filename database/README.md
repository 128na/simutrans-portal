# Database ディレクトリ

データベース関連のファイルを配置します。

## ディレクトリ構造

```
database/
├── factories/           # モデルファクトリー（テストデータ生成）
│   ├── ArticleFactory.php
│   ├── UserFactory.php
│   ├── AttachmentFactory.php
│   └── ...
├── migrations/          # マイグレーション（テーブル定義・変更）
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 2024_01_01_000000_create_articles_table.php
│   └── ...
├── schema/             # スキーマダンプ（SQLite用）
│   └── mysql-schema.sql
├── seeders/            # シーダー（初期データ投入）
│   ├── DatabaseSeeder.php
│   ├── ProdSeeder.php
│   ├── DuskSeeder.php
│   └── ...
└── .gitignore
```

## Migrations（マイグレーション）

テーブル定義と変更履歴を管理します。

### 最近の主要マイグレーション

```
2025_11_24_000000_create_article_link_check_histories_table.php
  - デッドリンクチェック履歴をDB化（キャッシュからの移行）

2025_11_16_000000_create_article_search_index_table.php
  - 記事検索インデックステーブル追加

2025_11_16_000001_drop_bulk_zips_table.php
  - 一括ダウンロード機能廃止に伴いテーブル削除

2025_11_08_000000_add_size_column_in_attachments.php
  - 添付ファイルサイズカラム追加

2025_11_07_000000_add_indexes.php
  - パフォーマンス改善のためのインデックス追加
```

### マイグレーション実行

```bash
# すべてのマイグレーションを実行
php artisan migrate

# 直前のマイグレーションをロールバック
php artisan migrate:rollback

# すべてロールバックして再実行
php artisan migrate:fresh

# シーダーも同時実行
php artisan migrate:fresh --seed
```

### マイグレーション作成

```bash
# テーブル作成
php artisan make:migration create_articles_table

# カラム追加
php artisan make:migration add_status_to_articles_table

# 外部キー追加
php artisan make:migration add_foreign_key_to_articles_table
```

### マイグレーション例

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('status', 32);
            $table->string('post_type', 32);
            $table->json('contents')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
```

## Seeders（シーダー）

初期データを投入します。

### DatabaseSeeder

エントリーポイント。環境に応じて適切なシーダーを呼び出します。

```php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProdSeeder::class,  // 本番環境用データ
        ]);
    }
}
```

### ProdSeeder

本番環境用の初期データを投入します。

- システム設定（ControllOptionsSeeder）
- カテゴリ（pak, addon-type, license）
- 固定ページ

### DuskSeeder

E2Eテスト（Laravel Dusk）用のテストデータを投入します。

- テストユーザー
- テスト記事
- テストカテゴリ

### ControllOptionsSeeder

システム設定の初期値を投入します。

```php
class ControllOptionsSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            ['key' => 'registration.invite', 'value' => '1'],
            ['key' => 'discord.webhook', 'value' => ''],
            // ...
        ];

        foreach ($options as $option) {
            ControllOption::updateOrCreate(
                ['key' => $option['key']],
                $option
            );
        }
    }
}
```

### シーダー実行

```bash
# すべてのシーダーを実行
php artisan db:seed

# 特定のシーダーを実行
php artisan db:seed --class=ProdSeeder

# マイグレーションと同時実行
php artisan migrate:fresh --seed
```

### シーダー作成

```bash
php artisan make:seeder CategorySeeder
```

```php
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['type' => 'pak', 'slug' => 'pak64', 'name' => 'pak64'],
            ['type' => 'pak', 'slug' => 'pak128', 'name' => 'pak128'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
```

## Factories（ファクトリー）

テストデータを生成します。

### ArticleFactory

```php
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'slug' => $this->faker->slug(),
            'title' => $this->faker->sentence(),
            'status' => ArticleStatus::Draft,
            'post_type' => ArticlePostType::Markdown,
            'contents' => [],
            'published_at' => null,
        ];
    }

    /**
     * 公開済み記事
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => ArticleStatus::Publish,
            'published_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    /**
     * アドオン投稿
     */
    public function addonPost(): static
    {
        return $this->state(fn(array $attributes) => [
            'post_type' => ArticlePostType::AddonPost,
            'contents' => [
                ['type' => 'text', 'text' => $this->faker->paragraph()],
            ],
        ]);
    }
}
```

### 使用例

```php
// 基本的な記事を作成
$article = Article::factory()->create();

// 公開済み記事を作成
$article = Article::factory()->published()->create();

// 特定の属性を上書き
$article = Article::factory()->create([
    'title' => 'My Test Article',
]);

// 複数作成
$articles = Article::factory()->count(10)->create();

// 関連データも作成
$articles = Article::factory()
    ->has(Tag::factory()->count(3))
    ->has(Attachment::factory()->count(2))
    ->create();
```

## Schema Dump

SQLiteテスト用のスキーマダンプです。

```bash
# スキーマダンプを生成
php artisan schema:dump

# マイグレーションを削除してダンプのみ使用
php artisan schema:dump --prune
```

## ベストプラクティス

### マイグレーション

✅ **推奨**:

- マイグレーション名は明確に（`create_articles_table`, `add_status_to_articles`）
- `up()` と `down()` の両方を実装
- インデックスを適切に設定
- 外部キー制約を使用

❌ **避けるべき**:

- データの投入（Seederを使用）
- 大規模なデータ変換（Command/Jobを使用）
- ロールバック不可能な変更

### シーダー

✅ **推奨**:

- 環境別に分離（ProdSeeder, DuskSeeder）
- `updateOrCreate()` で冪等性を確保
- 依存関係を明確に

❌ **避けるべき**:

- 大量のデータ投入（ファクトリーを使用）
- 環境固有の設定（.envを使用）

### ファクトリー

✅ **推奨**:

- デフォルト値を適切に設定
- State メソッドでバリエーション提供
- リレーションを簡単に作成できるように

❌ **避けるべき**:

- 本番データに依存
- 複雑すぎるロジック

## 関連ドキュメント

- **[Models](../app/Models/README.md)** - Eloquent Model
- **[Enums](../app/Enums/README.md)** - Enum定義
- **[README.md](../README.md)** - プロジェクト概要
