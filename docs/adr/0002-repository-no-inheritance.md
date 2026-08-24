# ADR: Repository は継承を使わず独立したクラスとして実装する

> ステータス: Accepted (2025-11-24)
> 由来: `docs/knowledge/repository-pattern-refactoring-20260103-knowledge.md` を統合

## 背景と問題

`BaseRepository` を継承する設計では次の問題があった:

- 使わないメソッド（`all()` 等）まで継承され、リスコフの置換原則に違反する
- 複数の BaseRepository 案が存在する場合にどれを継承すべきか不明確になる
- 関連メソッドが基底クラスと派生クラスに分散し、見通しが悪くなる

## 決定

`app/Repositories/**/*Repository.php` は `BaseRepository` などの継承を使わず、
各 Repository を独立したクラスとして実装する。モデルはコンストラクタで
`public readonly` プロパティとして受け取り、必要なメソッドのみ明示的に実装する。
共通の CRUD 操作は継承ではなく `HasCrud` トレイトの `use` で共有する
（[CLAUDE.md](../../CLAUDE.md) の Repository 層設計方針を参照）。

```php
// ✅ 独立クラス、必要なメソッドのみ実装
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
}

// ❌ Anti-pattern: 継承+ ビジネスロジックの混入
class ArticleRepository extends BaseRepository
{
    public function publishArticle(Article $article): void
    {
        // ビジネスロジックは Action に属する。Repository に書かない。
        $article->published_at = now();
        $article->save();
    }
}
```

`BaseRepository` は本決定の実施により既にリポジトリから削除済み（`app/Repositories/`
に継承元クラスは存在しない）。現在の実装パターンとディレクトリ構造は
[app/Repositories/README.md](../../app/Repositories/README.md) を参照する
（コードに最も近い場所にあるため、実装変更時にここが追随する）。

## 検討した代替案と却下理由

- 案A（BaseRepository 継承を維持しメソッドを絞る）:
  → 継承経路が残る限り「どのメソッドが実際に使われるか」を確認するコストが消えない。却下。

## 影響・トレードオフ

- 共通コードの再利用は継承ではなく `HasCrud` トレイトに一本化される。トレイトで
  賄えない共通化が必要になった場合は改めて設計判断が必要（[CLAUDE.md](../../CLAUDE.md) 参照）。
