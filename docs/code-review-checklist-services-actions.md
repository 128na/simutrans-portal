# コードレビューチェックリスト: Services と Actions

新しいコードのレビュー時に、Services と Actions の配置が適切かを確認するためのチェックリストです。

---

## 📋 レビュー時の基本チェック項目

### 1. 配置の妥当性

#### Services に配置されたクラスの場合

- [ ] **外部依存**: 外部APIやインフラと通信している？
- [ ] **汎用性**: 複数のドメインから利用される可能性がある？
- [ ] **技術的関心事**: ビジネスロジックを含んでいない？
- [ ] **ステートレス**: 内部状態を持たない実装になっている？
- [ ] **モック化可能**: テストで外部依存を容易にモック化できる？

**5項目中3項目以上がYesなら適切**

#### Actions に配置されたクラスの場合

- [ ] **ユースケース**: 特定の1つのユースケースを表現している？
- [ ] **単一責任**: 1つのメソッド（`__invoke()` または `execute()`）のみ？
- [ ] **ビジネスロジック**: ドメイン固有のビジネスルールを含む？
- [ ] **オーケストレーション**: 複数のRepository/Serviceを組み合わせている？
- [ ] **コントローラー直下**: コントローラーから直接呼び出される想定？

**5項目中3項目以上がYesなら適切**

---

## 🚨 レッドフラグ（即座に指摘すべき問題）

### Services でのレッドフラグ

```php
// ❌ ビジネスロジックを含む
class UserService
{
    public function registerUser(array $data)
    {
        // バリデーション
        // メール送信
        // DB保存
        // ポイント付与 ← ビジネスルール
        // → Actions/User/Registration へ移動すべき
    }
}

// ❌ 複数のユースケースが混在
class ArticleService
{
    public function create() {}
    public function update() {}
    public function delete() {}
    public function publish() {}
    // → 各ユースケースを独立した Action に分離すべき
}

// ❌ コントローラー専用のロジック
class FrontArticleService
{
    public function getArticlesForTopPage() {}
    // → 特定のユースケースなので Actions/ へ
}
```

### Actions でのレッドフラグ

```php
// ❌ 汎用的なユーティリティ
class FormatDateAction
{
    public function __invoke(Carbon $date): string
    {
        return $date->format('Y-m-d');
    }
    // → Services/Utility/ またはヘルパー関数へ
}

// ❌ 外部APIの単純なラッパー
class FetchTwitterDataAction
{
    public function __invoke(string $url): array
    {
        return $this->client->get($url);
    }
    // → Services/Twitter/ へ移動すべき
}

// ❌ 複数の責務
class ArticleManagerAction
{
    public function store() {}
    public function update() {}
    public function delete() {}
    // → 各メソッドを独立した Action に分離すべき
}
```

---

## ✅ 良いコード例

### Services の良い例

```php
<?php

namespace App\Services;

/**
 * ✅ 外部APIとの通信を抽象化
 * ✅ ステートレス
 * ✅ ビジネスロジックなし
 * ✅ モック化しやすい
 */
class TwitterV2Api
{
    public function __construct(
        private TwitterOAuth $client,
        private PKCEService $pkceService,
    ) {}

    public function postTweet(string $text): array
    {
        $this->applyPKCEToken();
        return $this->client->post('tweets', ['text' => $text]);
    }
}
```

**レビューコメント例**:

> ✅ 外部API連携として適切に実装されています。ステートレスでテスタビリティも高いです。

### Actions の良い例

```php
<?php

namespace App\Actions\Article;

/**
 * ✅ 単一のユースケース（記事作成）
 * ✅ ビジネスロジックを含む
 * ✅ 複数のService/Repositoryを組み合わせ
 * ✅ __invoke() 一つのみ
 */
class StoreArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private DecidePublishedAt $decidePublishedAt,
        private SyncRelatedModels $syncRelatedModels,
    ) {}

    public function __invoke(User $user, array $data): Article
    {
        // ビジネスルール: 公開日時の決定
        $publishedAt = ($this->decidePublishedAt)(
            $data['published_at'] ?? null,
            $data['status']
        );

        // 記事を保存
        $article = $this->articleRepository->store([
            'user_id' => $user->id,
            'published_at' => $publishedAt,
            // ...
        ]);

        // 関連モデルの同期
        ($this->syncRelatedModels)($article, $data);

        // イベント発火
        event(new ArticleStored($article));

        return $article;
    }
}
```

**レビューコメント例**:

> ✅ 記事作成のユースケースとして適切に実装されています。単一責任の原則に従い、ビジネスロジックが明確です。

---

## 📝 命名のチェック

### Services

```php
// ✅ Good
MarkdownService
FileInfoService
TwitterV2Api
BlueSkyApiClient

// ❌ Bad
MarkdownUtil        // "Service" で統一すべき
MarkdownHelper      // "Service" で統一すべき
Markdown            // 役割が不明確
ArticleService      // 複数のユースケースを持つ可能性
```

**レビューコメント例**:

> `MarkdownUtil` は `MarkdownService` にリネームして、プロジェクト全体で命名を統一しましょう。

### Actions

```php
// ✅ Good - 動詞で始める
StoreArticle
UpdateArticle
DeleteArticle
Registration

// ✅ Good - Action サフィックス
GenerateInviteCodeAction
ConversionAction

// ❌ Bad
ArticleStore        // 動詞が後ろ → StoreArticle
ArticleManager      // 複数責務を示唆
ArticleHandler      // 役割が不明確
ArticleCreate       // 動詞が後ろ → CreateArticle または StoreArticle
```

**レビューコメント例**:

> `ArticleStore` は `StoreArticle` にリネームして、動詞を前に配置しましょう。プロジェクト全体の規約に従います。

---

## 🧪 テストのチェック

### Services のテスト

```php
// ✅ Good - ユニットテスト
namespace Tests\Unit\Services;

class MarkdownServiceTest extends TestCase
{
    public function test_基本的なマークダウンをHTMLに変換できる()
    {
        $service = app(MarkdownService::class);
        $html = $service->toEscapedHTML('# Hello');

        $this->assertStringContainsString('<h1>Hello</h1>', $html);
    }
}

// ❌ Bad - データベースに依存
class MarkdownServiceTest extends TestCase
{
    use RefreshDatabase; // ← Services のテストでは不要

    public function test_記事を作成してマークダウン変換() // ← ユースケーステスト
    {
        $article = Article::factory()->create();
        // ...
    }
}
```

**レビューコメント例**:

> Services のテストはユニットテストとして実装し、データベースに依存しないようにしましょう。

### Actions のテスト

```php
// ✅ Good - 機能テスト
namespace Tests\Feature\Actions\Article;

class StoreArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_記事を作成できる()
    {
        $user = User::factory()->create();
        $action = app(StoreArticle::class);

        $article = $action($user, ['title' => 'Test']);

        $this->assertDatabaseHas('articles', ['title' => 'Test']);
    }
}

// ❌ Bad - 外部依存をモックしすぎ
class StoreArticleTest extends TestCase
{
    public function test_記事を作成できる()
    {
        $repository = $this->mock(ArticleRepository::class);
        // 全部モック ← Actions は統合的にテストすべき
    }
}
```

**レビューコメント例**:

> Actions のテストは機能テストとして実装し、実際のデータベースを使ってビジネスロジックを検証しましょう。

---

## 🔍 依存関係のチェック

### 適切な依存関係

```php
// ✅ Controller → Action → Service → Repository
class ArticleController
{
    public function store(StoreArticle $action)
    {
        return $action($request->user(), $request->validated());
    }
}

// ✅ Action → Action（オーケストレーション）
class StoreArticle
{
    public function __construct(
        private DecidePublishedAt $decidePublishedAt,
        private SyncRelatedModels $syncRelatedModels,
    ) {}
}

// ✅ Service → Service（適度な依存）
class TwitterV2Api
{
    public function __construct(
        private PKCEService $pkceService,
    ) {}
}
```

### 問題のある依存関係

```php
// ❌ Service → Action（逆転している）
class MarkdownService
{
    public function __construct(
        private StoreArticle $storeArticle, // ← Service が Action に依存
    ) {}
}

// ❌ Repository → Action（レイヤー違反）
class ArticleRepository
{
    public function __construct(
        private SendToTwitterAction $sendToTwitterAction, // ← Repository が Action に依存
    ) {}
}

// ❌ 循環依存
class ServiceA
{
    public function __construct(private ServiceB $serviceB) {}
}
class ServiceB
{
    public function __construct(private ServiceA $serviceA) {} // ← 循環参照
}
```

**レビューコメント例**:

> Service が Action に依存しています。依存の方向を見直し、適切なレイヤー構造にしましょう。

---

## 📊 レビューのまとめテンプレート

### 承認する場合

```markdown
## ✅ Services/Actions の配置レビュー

### 配置の妥当性

- ✅ Services: 外部API連携として適切
- ✅ Actions: 記事作成のユースケースとして適切

### 命名規則

- ✅ 命名規則に従っている

### テスト

- ✅ 適切なテストが実装されている

### 依存関係

- ✅ レイヤー構造が適切

**総評**: Services と Actions の責務分離が適切に実装されています。LGTM! 🎉
```

### 修正を依頼する場合

```markdown
## 🔄 Services/Actions の配置レビュー

### 配置の妥当性

- ❌ `UserService` にビジネスロジックが含まれている
  - 提案: `Actions/User/Registration` へ移動
  - 理由: ユーザー登録は特定のユースケースであり、ビジネスルールを含む

### 命名規則

- ❌ `ArticleStore` → `StoreArticle` にリネーム
  - 理由: 動詞を前に配置するプロジェクト規約に従う

### テスト

- ⚠️ Services のユニットテストが不足
  - 提案: 外部依存をモック化したテストを追加

**修正後に再レビューをお願いします。**
```

---

## 📚 参考資料

- **完全ガイド**: [docs/architecture-services-and-actions.md](./architecture-services-and-actions.md)
- **判断フローチャート**: [docs/decision-flowchart-services-actions.md](./decision-flowchart-services-actions.md)
- **クイックリファレンス**: [docs/quick-reference-services-actions.md](./quick-reference-services-actions.md)

---

**最終更新**: 2025-11-24
