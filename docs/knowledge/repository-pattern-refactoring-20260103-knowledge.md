# Repository パターンリファクタリング

Simutrans Portal における BaseRepository 廃止とリファクタリング履歴。

---

## 📖 概要

このドキュメントは、プロジェクトで「継承を使わない独立した Repository パターン」へのリファクタリング過程を記録しています。

### 主要な変更

- **廃止**: `BaseRepository` からの継承
- **採用**: 各 Repository は独立したクラス
- **メリット**: シンプル・明確・メンテナンス容易

---

## 🔄 リファクタリング前後

### Before: BaseRepository 継承パターン

```php
<?php

// app/Repositories/BaseRepository.php
abstract class BaseRepository
{
    protected $model;

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function all()
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->model->find($id)->update($data);
    }

    public function delete($id)
    {
        return $this->model->find($id)->delete();
    }
}

// app/Repositories/ArticleRepository.php
class ArticleRepository extends BaseRepository
{
    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    // 追加メソッド
    public function findBySlug(string $slug)
    {
        return $this->model->where('slug', $slug)->first();
    }
}

// 使用時
$repository = app(ArticleRepository::class);
$article = $repository->find(1);
$article = $repository->findBySlug('my-article');
```

**問題点:**

- ❌ 使わないメソッドまで継承（`all()` など）
- ❌ 複数の BaseRepository が存在する場合、どれを継承すべきか不明確
- ❌ リスコフの置換原則違反
- ❌ 関連メソッドが分散して不可視化

---

### After: 独立した Repository パターン

```php
<?php

// app/Repositories/ArticleRepository.php
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

// 使用時
$repository = app(ArticleRepository::class);
$article = $repository->find(1);
$article = $repository->findBySlug('my-article');
```

**メリット:**

- ✅ 必要なメソッドのみ明示的に実装
- ✅ 各 Repository が独立・完結
- ✅ IDE で自動補完が確実
- ✅ 依存関係が明確

---

## 📋 リファクタリング チェックリスト

Repository を新規作成または既存を修正する際:

### 設計

- [ ] `BaseRepository` から継承していない
- [ ] `$model` を `public readonly` プロパティで受け取る
- [ ] 必要なメソッドのみ実装
- [ ] メソッド名が用途を明確に表現

### メソッド設計

- [ ] 単体取得: `find()`, `findBy{条件}()`
- [ ] 一覧取得: `getFor{用途}()`, `getBy{条件}()`
- [ ] 作成: `store()`
- [ ] 更新: `update()`
- [ ] 削除: `delete()`
- [ ] 関連付け: `sync{関連名}()`

### テスト

- [ ] `tests/Feature/Repositories/` に配置
- [ ] データベース操作をテスト
- [ ] 複雑なクエリは実行結果をアサート

### ドキュメント

- [ ] PHPdoc が記述されている
- [ ] 戻り値の型が明示されている
- [ ] 例外が記述されている（必要な場合）

---

## 📚 既存 Repository の実装パターン

### ArticleRepository（メイン）

```php
class ArticleRepository
{
    public function __construct(public Article $model) {}

    public function find(int $id): ?Article
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?Article
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * スコープを使った検索
     */
    public function searchPublished(array $conditions): LengthAwarePaginator
    {
        $query = $this->model->publish()
            ->with(['user', 'categories', 'tags']);

        if (isset($conditions['keyword'])) {
            $query->where('title', 'LIKE', "%{$conditions['keyword']}%");
        }

        return $query->latest('published_at')->paginate(20);
    }

    public function store(array $data): Article
    {
        return $this->model->create($data);
    }

    public function update(Article $article, array $data): bool
    {
        return $article->update($data);
    }

    /**
     * 関連付けの同期
     */
    public function syncTags(Article $article, array $tagIds): void
    {
        $article->tags()->sync($tagIds);
    }

    public function syncCategories(Article $article, array $categoryIds): void
    {
        $article->categories()->sync($categoryIds);
    }
}
```

### UserRepository（シンプル）

```php
class UserRepository
{
    public function __construct(public User $model) {}

    public function find(int $id): ?User
    {
        return $this->model->with(['profile', 'articles'])->find($id);
    }

    public function store(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }
}
```

### TagRepository（ドメイン固有）

```php
class TagRepository
{
    public function __construct(public Tag $model) {}

    /**
     * 記事に紐づくタグを取得
     */
    public function getForEdit(?Article $article = null): Collection
    {
        $query = $this->model->orderBy('name');

        if ($article) {
            return $query->get();
        }

        return $query->get();
    }

    /**
     * 未使用タグを取得（削除対象）
     */
    public function getUnused(): Collection
    {
        return $this->model
            ->whereDoesntHave('articles')
            ->get();
    }

    public function store(array $data): Tag
    {
        return $this->model->create($data);
    }

    public function update(Tag $tag, array $data): bool
    {
        return $tag->update($data);
    }
}
```

---

## 🚫 アンチパターン

### ❌ BaseRepository を継承

```php
// ❌ やらないこと
class ArticleRepository extends BaseRepository
{
    public function __construct(Article $model)
    {
        $this->model = $model;
    }
}
```

**理由**: 使わないメソッドまで継承される

### ❌ 複数の責務を持つ Repository

```php
// ❌ やらないこと
class ArticleManager
{
    public function create() {}
    public function update() {}
    public function delete() {}
    public function publish() {}
    public function archive() {}
}
```

**理由**: 責務が多すぎて管理困難

### ❌ ビジネスロジックの実装

```php
// ❌ やらないこと
class ArticleRepository
{
    public function publishArticle(Article $article): void
    {
        // ビジネスロジック（公開日時決定）がここに
        $article->published_at = now();
        $article->save();
    }
}
```

**理由**: ビジネスロジックは Action に属する

---

## ✅ ベストプラクティス

### 型ヒント

```php
// ✅ Good: 明示的な型ヒント
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

### メソッド名の明確性

```php
// ✅ Good: 用途が明確
public function getForEdit()     // 編集フォーム用
public function getForDisplay()  // 表示用
public function getPublished()   // 公開済み

// ❌ Bad: 用途が不明確
public function getAll()
public function search()
public function find()
```

### Repository の使用方法

```php
// Controller で使用
class StoreArticleController
{
    public function __invoke(
        Request $request,
        ArticleRepository $repository
    ): JsonResponse {
        $article = $repository->store($request->validated());
        return response()->json($article);
    }
}

// Action で使用
class StoreArticle
{
    public function __construct(
        private ArticleRepository $repository
    ) {}

    public function __invoke(User $user, array $data): Article
    {
        return $this->repository->store([
            'user_id' => $user->id,
            ...$data,
        ]);
    }
}
```

---

## 📊 リファクタリング進捗

### 完了した Repository

- [x] ArticleRepository
- [x] UserRepository
- [x] TagRepository
- [x] CategoryRepository
- [x] AttachmentRepository
- [x] ArticleLinkCheckHistoryRepository

### BaseRepository 非推奨化

```php
// app/Repositories/BaseRepository.php
/**
 * @deprecated 継承を使わない独立した Repository パターンを採用しました
 * @see app/Repositories/README.md
 */
abstract class BaseRepository
{
    // ...
}
```

---

## 🧪 テスト例

Repository のテスト実装:

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

    public function test_find_by_slug_returns_article(): void
    {
        $article = Article::factory()->create(['slug' => 'test-article']);
        $repository = app(ArticleRepository::class);

        $result = $repository->findBySlug('test-article');

        $this->assertEquals($article->id, $result->id);
    }

    public function test_store_creates_article(): void
    {
        $repository = app(ArticleRepository::class);

        $result = $repository->store([
            'user_id' => 1,
            'slug' => 'test',
            'title' => 'Test',
        ]);

        $this->assertDatabaseHas('articles', ['slug' => 'test']);
    }
}
```

---

## 📝 マイグレーション ガイド（既存 Repository）

既存の BaseRepository を継承している Repository を修正する場合:

### Step 1: クラス定義を変更

```php
// Before
class ArticleRepository extends BaseRepository
{
    public function __construct(Article $model)
    {
        $this->model = $model;
    }
}

// After
class ArticleRepository
{
    public function __construct(public Article $model) {}
}
```

### Step 2: 必要なメソッドのみ実装

```php
// 使用している部分だけを明示的に実装
public function find(int $id): ?Article
{
    return $this->model->find($id);
}

public function store(array $data): Article
{
    return $this->model->create($data);
}
```

### Step 3: テストを更新

```php
// 既存テストは動作を変えずに、新しいパターンで動作確認
```

### Step 4: ドキュメントを更新

```php
// PHPdoc を追加
/**
 * IDで記事を取得
 */
public function find(int $id): ?Article
```

---

## 🎓 まとめ

| 項目           | Before              | After                |
| -------------- | ------------------- | -------------------- |
| **基底クラス** | BaseRepository 継承 | 独立したクラス       |
| **メソッド**   | 汎用的              | 用途別・ドメイン固有 |
| **テスト**     | 抽象化層でテスト    | 具体的な動作をテスト |
| **保守性**     | 複数継承経路        | 単一・明確           |
| **IDE 補完**   | 不確実              | 確実                 |

---

**最終更新**: 2025-11-24  
**関連**: [app/Repositories/README.md](../../app/Repositories/README.md)
