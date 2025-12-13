# Repositories ディレクトリ

データアクセス層を実装したクラスを配置します。

## 基本原則

- **継承を使用しない**: 各Repositoryは独立したクラス
- **必要なメソッドのみ実装**: 汎用的すぎる抽象化を避ける
- **モデルをプロパティとして受け取る**: `private readonly` で保持
- **ドメイン固有のメソッド名**: 用途が明確な命名

## ディレクトリ構造

```
Repositories/
├── Article/
│   ├── ArticleSearchIndexRepository.php  # 記事検索インデックス
│   ├── ConversionCountRepository.php     # ダウンロード数
│   └── ViewCountRepository.php           # 閲覧数
├── Attachment/
│   └── FileInfoRepository.php            # ファイル情報
├── User/
│   └── ProfileRepository.php             # プロフィール
├── ArticleLinkCheckHistoryRepository.php # リンク切れチェック履歴
├── ArticleRepository.php                 # 記事（メイン）
├── AttachmentRepository.php              # 添付ファイル
├── CategoryRepository.php                # カテゴリ
├── LoginHistoryRepository.php            # ログイン履歴
├── OauthTokenRepository.php              # OAuthトークン
├── RedirectRepository.php                # リダイレクト
├── TagRepository.php                     # タグ
├── UserRepository.php                    # ユーザー
├── BaseCountRepository.php               # 【非推奨】継承禁止
└── BaseRepository.php                    # 【非推奨】継承禁止
```

## 実装パターン

### 基本テンプレート

```php
<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;

class ArticleRepository
{
    public function __construct(public Article $model) {}

    /**
     * IDで記事を取得
     */
    public function find(int $id): ?Article
    {
        return $this->model->find($id);
    }

    /**
     * スラッグで記事を取得
     */
    public function findBySlug(string $slug): ?Article
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * 記事を作成
     */
    public function store(array $data): Article
    {
        return $this->model->create($data);
    }

    /**
     * 記事を更新
     */
    public function update(Article $article, array $data): bool
    {
        return $article->update($data);
    }
}
```

### 複雑なクエリの例

```php
/**
 * 公開済み記事を検索
 *
 * @return LengthAwarePaginator<Article>
 */
public function searchPublished(array $conditions): LengthAwarePaginator
{
    $query = $this->model->query()
        ->where('status', ArticleStatus::Publish)
        ->with(['user', 'categories', 'tags']);

    if (isset($conditions['keyword'])) {
        $query->where('title', 'LIKE', "%{$conditions['keyword']}%");
    }

    if (isset($conditions['category_id'])) {
        $query->whereHas('categories', fn($q) =>
            $q->where('categories.id', $conditions['category_id'])
        );
    }

    return $query->latest('published_at')
        ->paginate(20);
}
```

### 関連付けの同期

```php
/**
 * タグを同期
 *
 * @param  array<int>  $tagIds
 */
public function syncTags(Article $article, array $tagIds): void
{
    $article->tags()->sync($tagIds);
}

/**
 * 添付ファイルを同期
 *
 * @param  array<int>  $attachmentIds
 */
public function syncAttachments(Article $article, array $attachmentIds): void
{
    $article->attachments()->sync($attachmentIds);
}
```

## 命名規則

### CRUD操作

- **単体取得**: `find()`, `findOrFail()`, `findBy{条件}()`
- **一覧取得**: `getFor{用途}()`, `getBy{条件}()`
- **作成**: `store()`
- **更新**: `update()`
- **削除**: `delete()`

### 特殊な操作

- **関連付け**: `sync{関連名}()`
- **カウント**: `count{対象}()`
- **カーソル取得**: `cursor{用途}()`
- **存在確認**: `exists{条件}()`

### 例

```php
// 単体取得
public function findBySlug(string $slug): ?Article

// 一覧取得
public function getForEdit(?Article $article = null): Collection
public function getByStatus(ArticleStatus $status): Collection

// 関連付け
public function syncTags(Article $article, array $tagIds): void

// カーソル取得（大量データ）
public function cursorCheckLink(): LazyCollection
```

## 非推奨パターン

### ❌ BaseRepositoryの継承

```php
// ❌ 避けるべきパターン
class ArticleRepository extends BaseRepository
{
    // 使わないメソッドまで継承してしまう
}
```

### ❌ 汎用的すぎる抽象化

```php
// ❌ 避けるべきパターン
public function findWhere(array $conditions): Collection
{
    // 汎用的すぎて用途が不明
}
```

### ❌ ビジネスロジックの混入

```php
// ❌ 避けるべきパターン
public function publishArticle(Article $article): void
{
    // 公開日時の決定などのビジネスロジックは Action へ
    $article->update([
        'status' => 'publish',
        'published_at' => now(),
    ]);
}
```

## 推奨パターン

### ✅ 独立したクラス

```php
// ✅ 推奨パターン
class ArticleRepository
{
    public function __construct(public Article $model) {}

    // 必要なメソッドのみ実装
}
```

### ✅ ドメイン固有のメソッド名

```php
// ✅ 推奨パターン
public function getForEdit(?Article $article = null): Collection
public function cursorCheckLink(): LazyCollection
public function searchPublished(array $conditions): LengthAwarePaginator
```

### ✅ 型ヒントの明示

```php
// ✅ 推奨パターン
/**
 * @return Collection<int,Article>
 */
public function getForEdit(?Article $article = null): Collection
{
    return $this->model->query()
        ->with(['user', 'categories'])
        ->get();
}
```

## テスト

Repositoryのテストは `tests/Feature/Repositories/` に配置します。

```php
class ArticleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_returns_article(): void
    {
        $article = Article::factory()->create();
        $repository = app(ArticleRepository::class);

        $result = $repository->find($article->id);

        $this->assertEquals($article->id, $result->id);
    }
}
```

## 関連ドキュメント

- **[README.md](../../README.md)** - プロジェクト概要
- **[Services と Actions](../../docs/architecture-services-and-actions.md)** - アーキテクチャ全体
