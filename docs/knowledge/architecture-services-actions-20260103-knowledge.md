````markdown
# Services と Actions の役割分担ガイドライン

## 概要

このドキュメントは、`app/Services/` と `app/Actions/` ディレクトリの明確な責務の境界と配置基準を定義します。

## 目次

- [アーキテクチャの原則](#アーキテクチャの原則)
- [Services: 外部依存の抽象化層](#services-外部依存の抽象化層)
- [Actions: ユースケース・ビジネスロジック](#actions-ユースケースビジネスロジック)
- [判断フローチャート](#判断フローチャート)
- [命名規則](#命名規則)
- [ディレクトリ構造](#ディレクトリ構造)
- [実装例とアンチパターン](#実装例とアンチパターン)
- [テスト戦略](#テスト戦略)
- [既存コードの分析](#既存コードの分析)

---

## アーキテクチャの原則

### 基本方針

1. **単一責任の原則 (SRP)**: 各クラスは1つの明確な責務のみを持つ
2. **依存性逆転の原則 (DIP)**: 高レベルのモジュールは低レベルのモジュールに依存しない
3. **テスタビリティ**: 外部依存は容易にモック化できる
4. **明確な境界**: どこに何を配置するか迷わない

### レイヤー構造

```
Controller (HTTP Layer)
    ↓
Actions (Use Case Layer)
    ↓
Services + Repositories (Infrastructure & Domain Layer)
    ↓
Models (Domain Layer)
```

---

## Services: 外部依存の抽象化層

### 責務

Services は**技術的な関心事**を扱い、以下の役割を担います：

1. **外部APIとの通信**
   - Twitter, Discord, BlueSky, Misskey, Google 等のAPI連携
   - 認証、リクエスト/レスポンスの正規化
2. **インフラストラクチャのラッパー**
   - ファイルシステム操作
   - キャッシュ操作
   - メール送信
   - ストレージ操作

3. **汎用的なユーティリティ**
   - Markdown変換
   - Feed生成
   - 画像処理
   - データ変換

4. **複数のドメインから利用される基盤機能**
   - アプリケーション全体で再利用される横断的な機能

### 特徴

- ✅ **ステートレス**: 内部状態を持たず、入力に対して常に同じ出力を返す
- ✅ **副作用が明確**: 外部システムへの影響範囲が明確
- ✅ **テストでモック化しやすい**: インターフェースが単純明快
- ✅ **技術的な関心事に集中**: ビジネスルールを含まない

### 実装パターン

```php
<?php

namespace App\Services;

use GuzzleHttp\Client;

/**
 * 外部APIとの通信を担当
 * ビジネスロジックは含まない
 */
class ExampleApiService
{
    public function __construct(
        private Client $client,
        private string $apiKey,
    ) {}

    /**
     * APIからデータを取得
     *
     * @throws ApiException
     */
    public function fetchData(string $endpoint): array
    {
        $response = $this->client->get($endpoint, [
            'headers' => ['Authorization' => "Bearer {$this->apiKey}"],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
```

### Good Examples (適切な配置)

```php
// ✅ 外部API連携
Services\Twitter\TwitterV2Api
Services\Discord\InviteService
Services\BlueSky\BlueSkyApiClient
Services\Misskey\MisskeyApiClient

// ✅ インフラストラクチャ
Services\FileInfo\FileInfoService
Services\FileInfo\ZipArchiveParser

// ✅ 汎用ユーティリティ
Services\MarkdownService  // Markdown → HTML変換
Services\FeedService      // RSS/Atom フィード生成
```

### Anti-patterns (避けるべきパターン)

```php
// ❌ ビジネスロジックを含む
class UserService
{
    public function registerUser(array $data)
    {
        // バリデーション、ビジネスルール、DB保存が混在
        // → Actions へ移動すべき
    }
}

// ❌ 特定のユースケースに依存
class ArticlePublishService
{
    // 記事公開という具体的なユースケース
    // → Actions\Article\PublishArticle へ移動すべき
}
```

---

## Actions: ユースケース・ビジネスロジック

### 責務

Actions は**ビジネスの関心事**を扱い、以下の役割を担います：

1. **1つの具体的なユースケースを表現**
   - 「記事を作成する」「ユーザーを登録する」など
   - コントローラーから直接呼び出される
2. **アプリケーション固有のビジネスルール**
   - ドメイン特有のバリデーション
   - 状態遷移のロジック
   - 権限チェック

3. **複数のRepository/Serviceを組み合わせた処理**
   - オーケストレーション層
   - トランザクション境界の管理
4. **単一責任の原則（SRP）に従う**
   - 1クラス = 1ユースケース

### 特徴

- ✅ **1クラス = 1ユースケース**: 明確な単一責任
- ✅ **`__invoke()` または `execute()` メソッド1つ**: シンプルなインターフェース
- ✅ **ドメインロジックに集中**: 技術的な詳細は Service に委譲
- ✅ **コントローラーから呼び出される**: 薄いコントローラーの実現

### 実装パターン

```php
<?php

namespace App\Actions\Article;

use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Services\MarkdownService;

/**
 * 記事作成のユースケース
 * ビジネスルールとオーケストレーションを担当
 */
class StoreArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private MarkdownService $markdownService,
        private SyncRelatedModels $syncRelatedModels,
    ) {}

    /**
     * 記事を作成する
     */
    public function __invoke(User $user, array $data): Article
    {
        // ビジネスルール: ユーザーの記事作成権限をチェック
        if (!$user->canCreateArticle()) {
            throw new UnauthorizedException();
        }

        // Markdown変換（Service を利用）
        $html = $this->markdownService->toEscapedHTML($data['content']);

        // 記事を作成（Repository を利用）
        $article = $this->articleRepository->store([
            'user_id' => $user->id,
            'title' => $data['title'],
            'content' => $html,
        ]);

        // 関連モデルの同期（別の Action を利用）
        ($this->syncRelatedModels)($article, $data);

        // イベントの発火
        event(new ArticleStored($article));

        return $article;
    }
}
```

### Good Examples (適切な配置)

```php
// ✅ 具体的なユースケース
Actions\Article\StoreArticle        // 記事作成
Actions\Article\UpdateArticle       // 記事更新
Actions\User\Registration           // ユーザー登録

// ✅ 特定の業務プロセス
Actions\Analytics\FindArticles      // アナリティクス記事検索
Actions\DeadLink\Check              // デッドリンクチェック
Actions\SendSNS\Article\ToTwitter   // SNS投稿

// ✅ ドメイン固有のロジック
Actions\Article\DecidePublishedAt   // 公開日時の決定ロジック
Actions\Article\SyncRelatedModels   // 関連モデルの同期
```

### Anti-patterns (避けるべきパターン)

```php
// ❌ 汎用ユーティリティ
class FormatDateAction
{
    // 日付フォーマットは汎用的な処理
    // → Services\Utility へ移動、またはヘルパー関数に
}

// ❌ 外部APIの単純なラッパー
class FetchTwitterDataAction
{
    // 外部API呼び出しのみ
    // → Services\Twitter へ移動すべき
}

// ❌ 複数の責務を持つ
class ArticleManagerAction
{
    public function store() {}
    public function update() {}
    public function delete() {}
    // → 各ユースケースごとに分割すべき
}
```

---

## 判断フローチャート

新しいクラスを作成する際の配置判断フロー：

```
┌─────────────────────────────────┐
│  新しいクラスを作成する必要がある  │
└────────────┬────────────────────┘
             │
             ▼
    ┌────────────────────┐
    │ 外部APIやインフラと  │  Yes
    │ 通信する？          ├──────────► Services/ExternalApi/ または
    └────────┬───────────┘              Services/Infrastructure/
             │ No
             ▼
    ┌────────────────────┐
    │ 複数のドメインで    │  Yes
    │ 再利用される？      ├──────────► Services/Utility/
    └────────┬───────────┘
             │ No
             ▼
    ┌────────────────────┐
    │ 特定のユースケース  │  Yes
    │ を表現する？        ├──────────► Actions/{Domain}/
    └────────┬───────────┘              (例: Actions/Article/)
             │ No
             ▼
    ┌────────────────────┐
    │ 純粋なドメイン      │  Yes
    │ ロジック？          ├──────────► Domain/{Domain}/ (将来的)
    └────────┬───────────┘              または Actions/ に配置
             │ No
             ▼
       既存パターンを再検討
```

### 具体的な判断例

| ケース                     | 配置先                              | 理由                               |
| -------------------------- | ----------------------------------- | ---------------------------------- |
| Twitter API呼び出し        | `Services/Twitter/`                 | 外部API連携                        |
| Markdown変換               | `Services/MarkdownService`          | 汎用ユーティリティ、複数箇所で利用 |
| 記事の作成                 | `Actions/Article/StoreArticle`      | 特定のユースケース                 |
| 記事公開日時の決定ロジック | `Actions/Article/DecidePublishedAt` | ドメイン固有のビジネスルール       |
| ファイル解析               | `Services/FileInfo/`                | インフラ層の処理                   |
| アナリティクスデータ取得   | `Actions/Analytics/FindArticles`    | 特定のユースケース                 |

---

## 命名規則

### Services の命名規則

**基本形式**: `{機能名}Service`

```php
// ✅ Good
MarkdownService      // Markdown処理サービス
FeedService          // Feed生成サービス
FileInfoService      // ファイル情報取得サービス

// ❌ Bad
MarkdownUtil         // Service で統一
MarkdownHelper       // Service で統一
Markdown             // 役割が不明確
```

**外部API連携の場合**: `{サービス名}ApiClient` または `{サービス名}Api`

```php
// ✅ Good
TwitterV2Api
BlueSkyApiClient
MisskeyApiClient

// ❌ Bad
TwitterService       // API連携であることが不明確
TwitterClient        // プロジェクト全体で統一
```

### Actions の命名規則

**基本形式**: **動詞で始める** または `{動詞}{対象}Action`

```php
// ✅ Good - 動詞で始める（推奨）
StoreArticle         // 記事を保存
UpdateArticle        // 記事を更新
DeleteArticle        // 記事を削除
Registration         // ユーザー登録

// ✅ Good - Action サフィックス
GenerateInviteCodeAction   // 招待コード生成
ConversionAction          // 変換処理
SearchAction              // 検索処理

// ❌ Bad
ArticleStore         // 動詞が後ろ
ArticleManager       // 複数の責務を示唆
ArticleHandler       // 役割が不明確
```

**ディレクトリ名**: ドメインまたは機能で分類

```php
Actions/
├── Article/           // 記事関連のユースケース
│   ├── StoreArticle.php
│   ├── UpdateArticle.php
│   └── DeleteArticle.php
├── User/              // ユーザー関連のユースケース
│   ├── Registration.php
│   └── UpdateProfile.php
└── Analytics/         // アナリティクス関連
    └── FindArticles.php
```

---

## ディレクトリ構造

### 推奨される構造

```
app/
├── Services/                    # 外部依存・基盤機能
│   ├── ExternalApi/            # 外部API連携（推奨グループ化）
│   │   ├── Twitter/
│   │   │   ├── TwitterV2Api.php
│   │   │   ├── PKCEService.php
│   │   │   └── Exceptions/
│   │   ├── Discord/
│   │   │   ├── InviteService.php
│   │   │   └── LogConverter.php
│   │   ├── BlueSky/
│   │   │   ├── BlueSkyApiClient.php
│   │   │   └── ResizeByFileSize.php
│   │   ├── Misskey/
│   │   │   └── MisskeyApiClient.php
│   │   └── Google/
│   │       └── Recaptcha/
│   │           └── RecaptchaService.php
│   │
│   ├── Infrastructure/          # インフラ層（推奨グループ化）
│   │   ├── FileInfo/
│   │   │   ├── FileInfoService.php
│   │   │   ├── ZipArchiveParser.php
│   │   │   └── Extractors/
│   │   └── Storage/             # 将来的な拡張
│   │
│   └── Utility/                 # 汎用ユーティリティ（推奨グループ化）
│       ├── MarkdownService.php
│       └── FeedService.php
│
├── Actions/                     # ユースケース
│   ├── Article/                # 記事関連
│   │   ├── StoreArticle.php
│   │   ├── UpdateArticle.php
│   │   ├── DecidePublishedAt.php
│   │   └── SyncRelatedModels.php
│   ├── User/                   # ユーザー関連
│   │   ├── Registration.php
│   │   └── UpdateProfile.php
│   ├── Analytics/              # アナリティクス
│   │   └── FindArticles.php
│   ├── DeadLink/               # デッドリンク
│   │   ├── Check.php
│   │   └── OnDead.php
│   └── SendSNS/                # SNS連携
│       └── Article/
│           ├── ToTwitter.php
│           ├── ToBluesky.php
│           └── ToMisskey.php
│
└── Domain/                      # 純粋なドメインロジック（将来的に検討）
    └── Article/
        └── ValueObjects/
```

### 現在のディレクトリ構造（参考）

現状は以下の構造になっています：

```
app/
├── Services/                    # 30ファイル
│   ├── Twitter/                # 外部API
│   ├── Discord/                # 外部API
│   ├── BlueSky/                # 外部API
│   ├── Misskey/                # 外部API
│   ├── Google/                 # 外部API
│   ├── FileInfo/               # ファイル処理
│   ├── Front/                  # フロント用（要検討）
│   ├── MarkdownService.php
│   └── FeedService.php
│
└── Actions/                     # 37ファイル
    ├── Article/
    ├── User/
    ├── Analytics/
    ├── DeadLink/
    ├── FrontArticle/
    ├── SendSNS/
    └── ...
```

---

## 実装例とアンチパターン

### 例1: Markdown処理

#### ✅ Good: Service として実装

```php
<?php

namespace App\Services;

use cebe\markdown\GithubMarkdown;
use HTMLPurifier;

/**
 * Markdown変換サービス
 * 汎用的な変換ロジックを提供
 */
class MarkdownService
{
    public function __construct(
        private GithubMarkdown $githubMarkdown,
        private HTMLPurifier $htmlPurifier,
    ) {}

    /**
     * MarkdownをサニタイズされたHTMLに変換
     */
    public function toEscapedHTML(string $markdown): string
    {
        $raw = $this->githubMarkdown->parse($markdown);
        return $this->htmlPurifier->purify($raw);
    }
}
```

**理由**:

- 汎用的なユーティリティ機能
- 複数のドメイン（記事、コメント等）で利用される
- ビジネスルールを含まない

### 例2: 記事作成

#### ✅ Good: Action として実装

```php
<?php

namespace App\Actions\Article;

use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;

/**
 * 記事作成のユースケース
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
        // ビジネスロジック: 公開日時の決定
        $publishedAt = ($this->decidePublishedAt)(
            $data['published_at'] ?? null,
            $data['status']
        );

        // 記事を保存
        $article = $this->articleRepository->store([
            'user_id' => $user->id,
            'title' => $data['title'],
            'published_at' => $publishedAt,
        ]);

        // 関連モデルを同期
        ($this->syncRelatedModels)($article, $data);

        // イベント発火
        dispatch(new JobUpdateRelated($article->id));
        event(new ArticleStored($article));

        return $article;
    }
}
```

**理由**:

- 特定のユースケース（記事作成）
- 複数のRepository/Serviceを組み合わせている
- ビジネスルール（公開日時の決定）を含む

#### ❌ Bad: Service として実装した場合

```php
<?php

namespace App\Services;

// ❌ Serviceにビジネスロジックを含めるのは不適切
class ArticleService
{
    public function createArticle(User $user, array $data)
    {
        // ビジネスロジックがServiceに混在
        // コントローラーから直接呼ばれる想定
        // → Actionとして実装すべき
    }

    public function updateArticle(Article $article, array $data)
    {
        // 複数のユースケースが1つのクラスに
        // → 各ユースケースをActionとして分離すべき
    }
}
```

### 例3: Twitter API連携

#### ✅ Good: Service として実装

```php
<?php

namespace App\Services\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Twitter API v2 クライアント
 * 外部API連携の抽象化層
 */
class TwitterV2Api extends TwitterOAuth
{
    public function __construct(
        string $consumerKey,
        string $consumerSecret,
        private readonly PKCEService $pKCEService,
    ) {
        parent::__construct($consumerKey, $consumerSecret);
    }

    /**
     * ツイートを投稿
     */
    public function postTweet(string $text): array
    {
        $this->applyPKCEToken();
        return $this->post('tweets', ['text' => $text]);
    }
}
```

**理由**:

- 外部APIとの通信
- 技術的な関心事に集中
- 複数のActionから利用される

### 例4: メタ情報生成

#### 🤔 検討が必要: Front/MetaOgpService

現在の実装:

```php
<?php

namespace App\Services\Front;

/**
 * OGPメタ情報生成サービス
 */
class MetaOgpService
{
    public function frontArticleShow(User $user, Article $article): array
    {
        return [
            'title' => $article->title.' - '.Config::string('app.name'),
            'description' => $this->trimDescription($article->contents->getDescription()),
            'image' => $article->thumbnail_url,
        ];
    }
}
```

**検討ポイント**:

- ビュー層のヘルパー的な役割
- ビジネスロジックは含まない
- 汎用性は低い（フロントページ専用）

**推奨**: 現状の配置で問題ないが、以下の選択肢も検討可能

1. `Services/View/MetaOgpService` へ移動（ビュー関連サービスとしてグループ化）
2. View Composer として実装
3. 現状維持（`Services/Front/`）

---

## テスト戦略

### Services のテスト

Services は**外部依存をモック化**してテストします。

```php
<?php

namespace Tests\Unit\Services;

use App\Services\MarkdownService;
use Tests\TestCase;

class MarkdownServiceTest extends TestCase
{
    private MarkdownService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(MarkdownService::class);
    }

    /**
     * @test
     */
    public function 基本的なマークダウンをHTMLに変換できる(): void
    {
        $markdown = '# Hello World';
        $html = $this->service->toEscapedHTML($markdown);

        $this->assertStringContainsString('<h1>Hello World</h1>', $html);
    }

    /**
     * @test
     */
    public function XSSをエスケープできる(): void
    {
        $markdown = '<script>alert("XSS")</script>';
        $html = $this->service->toEscapedHTML($markdown);

        $this->assertStringNotContainsString('<script>', $html);
    }
}
```

**ポイント**:

- ユニットテスト (`tests/Unit/Services/`)
- 外部依存はモック化
- 入力と出力の検証に集中

### Actions のテスト

Actions は**ビジネスロジック**をテストします。

```php
<?php

namespace Tests\Feature\Actions\Article;

use App\Actions\Article\StoreArticle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function 記事を作成できる(): void
    {
        $user = User::factory()->create();
        $action = app(StoreArticle::class);

        $data = [
            'article' => [
                'status' => 'publish',
                'title' => 'Test Article',
                'slug' => 'test-article',
                'post_type' => 'addon',
                'contents' => ['description' => 'Test content'],
            ],
        ];

        $article = $action($user, $data);

        $this->assertDatabaseHas('articles', [
            'user_id' => $user->id,
            'title' => 'Test Article',
        ]);

        $this->assertNotNull($article->published_at);
    }

    /**
     * @test
     */
    public function 公開日時が正しく設定される(): void
    {
        // ビジネスロジックのテスト
        // ...
    }
}
```

**ポイント**:

- 機能テスト (`tests/Feature/Actions/`)
- データベースを使用
- ビジネスルールの検証

---

## 既存コードの分析

### Services/ の分析結果

| カテゴリ           | ファイル                          | 配置評価 | 備考            |
| ------------------ | --------------------------------- | -------- | --------------- |
| **外部API**        | Twitter/TwitterV2Api              | ✅ 適切  | API連携の抽象化 |
|                    | Twitter/PKCEService               | ✅ 適切  | OAuth認証       |
|                    | Discord/InviteService             | ✅ 適切  | Discord API     |
|                    | BlueSky/BlueSkyApiClient          | ✅ 適切  | BlueSky API     |
|                    | Misskey/MisskeyApiClient          | ✅ 適切  | Misskey API     |
|                    | Google/Recaptcha/RecaptchaService | ✅ 適切  | reCAPTCHA検証   |
| **インフラ**       | FileInfo/FileInfoService          | ✅ 適切  | ファイル解析    |
|                    | FileInfo/ZipArchiveParser         | ✅ 適切  | ZIP処理         |
|                    | FileInfo/Extractors/\*            | ✅ 適切  | ファイル抽出    |
| **ユーティリティ** | MarkdownService                   | ✅ 適切  | Markdown変換    |
|                    | FeedService                       | ✅ 適切  | Feed生成        |
| **ビュー**         | Front/MetaOgpService              | 🤔 検討  | ビューヘルパー  |

**総評**: ほとんどのServicesは適切に配置されています。

### Actions/ の分析結果

| カテゴリ           | ファイル                  | 配置評価 | 備考                 |
| ------------------ | ------------------------- | -------- | -------------------- |
| **記事**           | Article/StoreArticle      | ✅ 適切  | 記事作成ユースケース |
|                    | Article/UpdateArticle     | ✅ 適切  | 記事更新ユースケース |
|                    | Article/DecidePublishedAt | ✅ 適切  | ビジネスルール       |
|                    | Article/SyncRelatedModels | ✅ 適切  | オーケストレーション |
| **ユーザー**       | User/Registration         | ✅ 適切  | ユーザー登録         |
|                    | User/UpdateProfile        | ✅ 適切  | プロフィール更新     |
| **アナリティクス** | Analytics/FindArticles    | ✅ 適切  | データ取得ロジック   |
| **デッドリンク**   | DeadLink/Check            | ✅ 適切  | チェックロジック     |
|                    | DeadLink/OnDead           | ✅ 適切  | イベントハンドラ     |
| **SNS**            | SendSNS/Article/ToTwitter | ✅ 適切  | SNS投稿ユースケース  |
|                    | SendSNS/Article/ToBluesky | ✅ 適切  | SNS投稿ユースケース  |
| **OAuth**          | Oauth/CallbackAction      | ✅ 適切  | OAuth認証フロー      |
| **リダイレクト**   | Redirect/AddRedirect      | ✅ 適切  | リダイレクト管理     |

**総評**: Actionsは明確なユースケースとして適切に分離されています。

### 改善の必要性

現状の配置は概ね適切であり、**大規模なリファクタリングは不要**です。

**推奨される改善（オプション）**:

1. **Services のグループ化**（将来的な拡張のため）

   ```
   Services/
   ├── ExternalApi/    # 外部API関連をグループ化
   ├── Infrastructure/ # インフラ層をグループ化
   └── Utility/        # ユーティリティをグループ化
   ```

2. **命名の統一**
   - Services: `〇〇Service` で統一
   - Actions: 動詞で始めるか `〇〇Action` で統一

3. **テストカバレッジの向上**
   - 特にServicesのユニットテスト
   - Actionsの機能テスト

---

## まとめ

### 重要なポイント

1. **Services = 技術的な関心事**
   - 外部API、インフラ、汎用ユーティリティ
   - ステートレス、モック化しやすい

2. **Actions = ビジネスの関心事**
   - 1クラス = 1ユースケース
   - ドメインロジック、オーケストレーション

3. **判断基準は明確**
   - 「複数のドメインで使う？」→ Services
   - 「特定のユースケース？」→ Actions

4. **既存コードは概ね適切**
   - 大規模な移動は不要
   - 新しいコードは本ガイドラインに従う

### 参考資料

- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [SOLID原則](https://en.wikipedia.org/wiki/SOLID)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [単一責任の原則 (SRP)](https://en.wikipedia.org/wiki/Single-responsibility_principle)

---

**最終更新**: 2025-11-24  
**バージョン**: 1.0.0
````
