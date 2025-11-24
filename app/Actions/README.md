# Actions ディレクトリ

ユースケース・ビジネスロジックを実装したクラスを配置します。

## 📖 詳細ドキュメント

- **[Services と Actions の役割分担ガイドライン](../../docs/architecture-services-and-actions.md)** - 詳細なアーキテクチャ説明
- **[配置判断フローチャート](../../docs/decision-flowchart-services-actions.md)** - 新しいクラスの配置を判断するガイド

## 基本原則

- **1クラス = 1ユースケース**: 単一責任の原則を徹底
- **`__invoke()` メソッド**: 1つのメソッドで完結
- **ビジネスロジック**: 技術的詳細はServiceに委譲
- **命名規則**: 動詞で始める（`StoreArticle`, `UpdateArticle`）

## ディレクトリ構造

```
Actions/
├── Analytics/           # アナリティクス
│   └── FindArticles.php
├── Article/             # 記事関連
│   ├── StoreArticle.php
│   ├── UpdateArticle.php
│   ├── DecidePublishedAt.php
│   └── SyncRelatedModels.php
├── ArticleSearchIndex/  # 記事検索インデックス
│   └── JobUpdateIndex.php
├── DeadLink/            # デッドリンクチェック
│   ├── Check.php
│   └── OnDead.php
├── DiscordInvite/       # Discord招待
│   └── Invite.php
├── Fortify/             # 認証
│   └── CreateNewUser.php
├── FrontArticle/        # フロント記事取得
│   └── GetForShow.php
├── GenerateStatic/      # 静的ファイル生成
│   └── GenerateAll.php
├── MFA/                 # 多要素認証
│   └── Enable2FA.php
├── Oauth/               # OAuth連携
│   ├── AuthoroizeAction.php
│   ├── CallbackAction.php
│   ├── RefreshAction.php
│   └── RevokeAction.php
├── Redirect/            # リダイレクト管理
│   ├── GetForIndex.php
│   └── Store.php
├── SendSNS/             # SNS投稿
│   └── Article/
│       ├── ToTwitter.php
│       ├── ToDiscord.php
│       ├── ToBlueSky.php
│       └── ToMisskey.php
├── StoreAttachment/     # 添付ファイル保存
│   └── StoreFromUpload.php
└── User/                # ユーザー関連
    └── Registration.php
```

## 実装パターン

### 基本テンプレート

```php
<?php

declare(strict_types=1);

namespace App\Actions\Domain;

use App\Models\Model;
use App\Repositories\ModelRepository;
use App\Services\SomeService;

/**
 * {ユースケースの説明}
 *
 * 例: 記事を作成する
 */
final readonly class ExampleAction
{
    public function __construct(
        private ModelRepository $repository,
        private SomeService $service,
    ) {}

    /**
     * {ユースケース}を実行する
     */
    public function __invoke(User $user, array $data): Model
    {
        // ビジネスルール検証
        if (!$user->canDoSomething()) {
            throw new UnauthorizedException();
        }

        // Serviceを使った技術的処理
        $processed = $this->service->process($data);

        // Repositoryを使ったデータ永続化
        $model = $this->repository->store($processed);

        // イベント発火
        event(new SomethingHappened($model));

        return $model;
    }
}
```

### 複数のService/Repositoryを組み合わせる例

```php
final readonly class StoreArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private DecidePublishedAt $decidePublishedAt,
        private SyncRelatedModels $syncRelatedModels,
    ) {}

    public function __invoke(User $user, array $data): Article
    {
        // ビジネスロジック: 公開日時の決定
        $publishedAt = ($this->decidePublishedAt)(
            $data['published_at'] ?? null,
            $data['status']
        );

        // 記事を保存
        $article = $this->articleRepository->store([
            'user_id' => $user->id,
            'published_at' => $publishedAt,
            ...$data,
        ]);

        // 関連モデルの同期（別のActionを利用）
        ($this->syncRelatedModels)($article, $data);

        return $article;
    }
}
```

## Services との違い

| 項目           | Actions            | Services           |
| -------------- | ------------------ | ------------------ |
| **責務**       | ビジネスロジック   | 技術的な処理       |
| **具体性**     | 特定のユースケース | 汎用的な機能       |
| **呼び出し元** | Controller         | Action, Controller |
| **例**         | StoreArticle       | MarkdownService    |
| **関心事**     | WHAT（何をするか） | HOW（どうやるか）  |

## テスト

Actionsのテストは `tests/Unit/Actions/` に配置します。

```php
final class StoreArticleTest extends TestCase
{
    public function test_記事を作成できる(): void
    {
        $user = User::factory()->create();
        $data = ['title' => 'Test', 'status' => 'publish'];

        $article = app(StoreArticle::class)($user, $data);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'user_id' => $user->id,
        ]);
    }
}
```

## 配置判断

新しいクラスを作成する際は、以下のフローで判断してください：

1. **外部APIやインフラと通信する？** → `Services/`
2. **複数のドメインで再利用される？** → `Services/`
3. **特定のユースケースを表現する？** → `Actions/` ✅
4. **純粋なドメインロジック？** → `Actions/` ✅

詳細は [配置判断フローチャート](../../docs/decision-flowchart-services-actions.md) を参照してください。
