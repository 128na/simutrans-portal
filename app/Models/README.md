# Models ディレクトリ

ドメインモデル（Eloquent Model）を配置します。

## 基本原則

- **単一のテーブルに対応**: 1モデル = 1テーブル
- **リレーション定義**: 関連するモデルとの関係を明示
- **Casts**: 属性の型変換を定義
- **Scopes**: 再利用可能なクエリ条件
- **Accessors/Mutators**: 属性の取得/設定時の変換

## ディレクトリ構造

```
Models/
├── Article/
│   ├── ArticleSearchIndex.php    # 記事検索インデックス
│   ├── ConversionCount.php       # ダウンロード数
│   └── ViewCount.php             # 閲覧数
├── Attachment/
│   └── FileInfo.php              # ファイル情報
├── Contents/
│   └── Section.php               # セクションコンテンツ
├── User/
│   ├── LoginHistory.php          # ログイン履歴
│   └── Profile.php               # プロフィール
├── Article.php                   # 記事（メイン）
├── ArticleLinkCheckHistory.php   # リンク切れチェック履歴
├── Attachment.php                # 添付ファイル
├── Category.php                  # カテゴリ
├── ControllOption.php            # 制御オプション
├── OauthToken.php                # OAuthトークン
├── Redirect.php                  # リダイレクト
├── Tag.php                       # タグ
└── User.php                      # ユーザー
```

## 主要モデル

### Article（記事）

- **テーブル**: `articles`
- **主要属性**: `user_id`, `slug`, `title`, `status`, `post_type`, `contents`, `published_at`
- **リレーション**:
  - `user()` - 作成者
  - `categories()` - カテゴリ（多対多）
  - `tags()` - タグ（多対多）
  - `attachments()` - 添付ファイル（多対多）
  - `viewCount()` - 閲覧数
  - `conversionCount()` - ダウンロード数
- **Casts**:
  - `status` → `ArticleStatus` (Enum)
  - `post_type` → `ArticlePostType` (Enum)
  - `contents` → `Contents\Section` (Collection)
  - `published_at` → `datetime`
- **Scopes**:
  - `scopePublish()` - 公開済み
  - `scopeByUser()` - ユーザー別

### User（ユーザー）

- **テーブル**: `users`
- **主要属性**: `name`, `email`, `role`, `two_factor_secret`
- **リレーション**:
  - `articles()` - 記事
  - `profile()` - プロフィール
  - `loginHistories()` - ログイン履歴
  - `oauthTokens()` - OAuthトークン
- **Casts**:
  - `role` → `UserRole` (Enum)
  - `email_verified_at` → `datetime`
  - `two_factor_confirmed_at` → `datetime`

### Attachment（添付ファイル）

- **テーブル**: `attachments`
- **主要属性**: `user_id`, `file`, `type`, `caption`, `size`
- **リレーション**:
  - `user()` - 作成者
  - `articles()` - 記事（多対多）
  - `fileInfo()` - ファイル情報
- **Casts**:
  - `size` → `integer`

### Category（カテゴリ）

- **テーブル**: `categories`
- **主要属性**: `type`, `slug`, `name`, `description`, `order`
- **リレーション**:
  - `articles()` - 記事（多対多）
- **Casts**:
  - `type` → `CategoryType` (Enum)
  - `order` → `integer`

### Tag（タグ）

- **テーブル**: `tags`
- **主要属性**: `name`, `editable`
- **リレーション**:
  - `articles()` - 記事（多対多）
- **Casts**:
  - `editable` → `boolean`

## 実装パターン

### 基本構造

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ArticleStatus::class,
            'published_at' => 'datetime',
        ];
    }

    // リレーション
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

### リレーション定義

```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// 1対1
public function profile(): HasOne
{
    return $this->hasOne(Profile::class);
}

// 多対1
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

// 1対多
public function articles(): HasMany
{
    return $this->hasMany(Article::class);
}

// 多対多
public function tags(): BelongsToMany
{
    return $this->belongsToMany(Tag::class, 'article_tags')
        ->withTimestamps();
}

// 多対多（中間テーブルのカラム付き）
public function attachments(): BelongsToMany
{
    return $this->belongsToMany(Attachment::class, 'article_attachments')
        ->withPivot('order')
        ->withTimestamps();
}
```

### Scopes

```php
use Illuminate\Database\Eloquent\Builder;

/**
 * 公開済み記事のみ
 */
public function scopePublish(Builder $query): void
{
    $query->where('status', ArticleStatus::Publish)
        ->where('published_at', '<=', now());
}

/**
 * ユーザー別
 */
public function scopeByUser(Builder $query, int $userId): void
{
    $query->where('user_id', $userId);
}

// 使用例
Article::publish()->latest('published_at')->get();
Article::byUser($user->id)->get();
```

### Accessors/Mutators

```php
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * タイトルを常に大文字で取得
 */
protected function title(): Attribute
{
    return Attribute::make(
        get: fn(?string $value) => strtoupper($value),
        set: fn(?string $value) => strtolower($value),
    );
}
```

### Casts（カスタム型変換）

```php
use App\Casts\ContentsSectionsCast;

protected function casts(): array
{
    return [
        'status' => ArticleStatus::class,        // Enum
        'contents' => ContentsSectionsCast::class, // カスタムCast
        'published_at' => 'datetime',            // Carbon
        'is_active' => 'boolean',                // bool
        'meta' => 'array',                       // JSON→配列
    ];
}
```

## Enum統合

Laravel 11以降、Enumを直接Castできます。

```php
// app/Enums/ArticleStatus.php
enum ArticleStatus: string
{
    case Draft = 'draft';
    case Publish = 'publish';
    case Private = 'private';
}

// app/Models/Article.php
protected function casts(): array
{
    return [
        'status' => ArticleStatus::class,
    ];
}

// 使用例
$article->status = ArticleStatus::Publish;
if ($article->status === ArticleStatus::Draft) {
    // ...
}
```

## ファクトリー

テストデータ生成用のFactoryは `database/factories/` に配置します。

```php
// database/factories/ArticleFactory.php
use App\Models\Article;

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
        ];
    }

    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => ArticleStatus::Publish,
            'published_at' => now(),
        ]);
    }
}

// 使用例
Article::factory()->published()->create();
```

## 命名規則

### モデル名

- **単数形**: `Article`, `User`, `Tag`
- **パスカルケース**: `ArticleSearchIndex`

### メソッド名

- **リレーション**: 単数形（BelongsTo, HasOne）、複数形（HasMany, BelongsToMany）
- **Scope**: `scope` プレフィックス + パスカルケース
- **Accessor/Mutator**: スネークケース

### 例

```php
// ✅ 正しい
public function user(): BelongsTo
public function articles(): HasMany
public function scopePublish(Builder $query): void
protected function fullName(): Attribute

// ❌ 間違い
public function users(): BelongsTo  // 単数形にすべき
public function article(): HasMany  // 複数形にすべき
public function publish(Builder $query): void  // scopeプレフィックス必要
```

## テスト

モデルのテストは `tests/Unit/Models/` に配置します。

```php
class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relationship(): void
    {
        $article = Article::factory()->create();

        $this->assertInstanceOf(User::class, $article->user);
    }

    public function test_publish_scope(): void
    {
        Article::factory()->create(['status' => ArticleStatus::Draft]);
        Article::factory()->published()->create();

        $this->assertCount(1, Article::publish()->get());
    }
}
```

## 関連ドキュメント

- **[README.md](../../README.md)** - プロジェクト概要
- **[Enums](../Enums/README.md)** - Enum定義
- **[Casts](../Casts/README.md)** - カスタムCast
- **[Repositories](../Repositories/README.md)** - データアクセス層
