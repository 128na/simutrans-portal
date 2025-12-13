# Services と Actions クイックリファレンス

新しいクラスを作成する際の配置判断を素早く行うための簡易リファレンスカードです。

---

## 📋 判断チャート（30秒で決める）

```
質問1: 外部APIやインフラと通信する？
  → YES: Services/

質問2: 複数のドメインで使える汎用機能？
  → YES: Services/

質問3: 特定のユースケースを表現する？
  → YES: Actions/

それ以外: 既存パターンを再確認
```

---

## 🎯 Services vs Actions 一覧表

| 観点         | Services                     | Actions                      |
| ------------ | ---------------------------- | ---------------------------- |
| **責務**     | 技術的な関心事               | ビジネスの関心事             |
| **目的**     | 外部依存の抽象化             | ユースケースの実装           |
| **再利用性** | 高い（複数ドメインから利用） | 低い（特定ユースケース専用） |
| **ステート** | ステートレス                 | ステートレス                 |
| **依存**     | インフラ・ライブラリ         | Repository/Service           |
| **テスト**   | モックで外部依存を置換       | ビジネスロジックを検証       |
| **メソッド** | 複数の公開メソッドOK         | `__invoke()` 1つ推奨         |
| **命名**     | `{機能}Service`              | `{動詞}{対象}`               |

---

## ✅ Services の典型例

### 配置すべきもの

```php
// 外部API連携
Services/Twitter/TwitterV2Api
Services/Discord/InviteService
Services/BlueSky/BlueSkyApiClient

// インフラ層
Services/FileInfo/FileInfoService
Services/Storage/S3StorageService

// 汎用ユーティリティ
Services/MarkdownService
Services/FeedService
Services/ImageResizeService
```

### 特徴

- ✅ ステートレス
- ✅ 技術的な処理に集中
- ✅ ビジネスルールなし
- ✅ モック化しやすい

---

## ✅ Actions の典型例

### 配置すべきもの

```php
// CRUD系ユースケース
Actions/Article/StoreArticle
Actions/Article/UpdateArticle
Actions/User/Registration

// 複雑なビジネスプロセス
Actions/Analytics/FindArticles
Actions/DeadLink/Check
Actions/SendSNS/Article/ToTwitter

// ドメイン固有ロジック
Actions/Article/DecidePublishedAt
Actions/Article/SyncRelatedModels
```

### 特徴

- ✅ 1クラス = 1ユースケース
- ✅ ビジネスルールを含む
- ✅ 複数のService/Repositoryを組み合わせ
- ✅ コントローラーから直接呼び出し

---

## 🚫 よくある間違い

### ❌ Services に配置すべきでないもの

```php
// ❌ 特定のユースケース
Services/ArticlePublishService  // → Actions/Article/PublishArticle

// ❌ ビジネスロジック
Services/UserService {
    public function registerUser() {} // → Actions/User/Registration
    public function updateProfile() {} // → Actions/User/UpdateProfile
}

// ❌ ドメイン固有の処理
Services/ArticleValidator  // → Actions/ または Validator
```

### ❌ Actions に配置すべきでないもの

```php
// ❌ 汎用ユーティリティ
Actions/FormatDateAction  // → Services/ またはヘルパー

// ❌ 外部APIラッパー
Actions/FetchTwitterDataAction  // → Services/Twitter/

// ❌ 複数の責務
Actions/ArticleManagerAction {
    public function store() {}
    public function update() {}
    public function delete() {}  // → 各々を独立したActionに
}
```

---

## 📐 命名規則

### Services

```php
// ✅ Good
MarkdownService
FileInfoService
TwitterV2Api
BlueSkyApiClient

// ❌ Bad
MarkdownUtil        // Service で統一
MarkdownHelper      // Service で統一
Markdown            // 役割不明確
```

### Actions

```php
// ✅ Good - 動詞で始める（推奨）
StoreArticle
UpdateArticle
Registration

// ✅ Good - Action サフィックス
GenerateInviteCodeAction
ConversionAction

// ❌ Bad
ArticleStore        // 動詞が後ろ
ArticleManager      // 複数責務を示唆
ArticleHandler      // 役割不明確
```

---

## 🔍 実装パターン

### Service のテンプレート

```php
<?php

namespace App\Services;

/**
 * {何をする}サービス
 * {技術的な処理の説明}
 */
class ExampleService
{
    public function __construct(
        private Client $client,
        private string $apiKey,
    ) {}

    /**
     * {何を}する
     *
     * @throws ExampleException
     */
    public function doSomething(string $input): string
    {
        // 技術的な処理
        return $result;
    }
}
```

### Action のテンプレート

```php
<?php

namespace App\Actions\Domain;

use App\Models\Model;

/**
 * {ユースケースの説明}
 */
class ExampleAction
{
    public function __construct(
        private ModelRepository $repository,
        private ExampleService $service,
    ) {}

    /**
     * {ユースケースを}実行する
     */
    public function __invoke(Model $model, array $data): Model
    {
        // ビジネスロジック
        // 複数のService/Repositoryを組み合わせ
        return $result;
    }
}
```

---

## 🧪 テストの配置

```
tests/
├── Unit/
│   └── Services/           # Services のユニットテスト
│       ├── MarkdownServiceTest.php
│       └── FeedServiceTest.php
│
└── Feature/
    └── Actions/            # Actions の機能テスト
        ├── Article/
        │   ├── StoreArticleTest.php
        │   └── UpdateArticleTest.php
        └── User/
            └── RegistrationTest.php
```

---

## 💡 判断に迷ったら

### パターン1: Service と Action の境界

**例:** 記事の公開日時を決定するロジック

- ドメイン固有？ **YES** → `Actions/Article/DecidePublishedAt`
- 汎用的？ **NO** → Services には配置しない

### パターン2: 複数の責務を持つ場合

**例:** ArticleService が複数のメソッドを持つ

```php
// ❌ Bad
class ArticleService {
    public function create() {}
    public function update() {}
    public function delete() {}
}

// ✅ Good - 各ユースケースに分離
Actions/Article/StoreArticle
Actions/Article/UpdateArticle
Actions/Article/DeleteArticle
```

### パターン3: 既存パターンとの整合性

迷ったら既存の似た機能がどこに配置されているか確認:

```bash
# 類似クラスを検索
find app/Services -name "*Twitter*"
find app/Actions -name "*Article*"
```

---

## 📚 詳細ドキュメント

- **完全ガイド**: [docs/architecture-services-and-actions.md](./architecture-services-and-actions.md)
- **判断フローチャート**: [docs/decision-flowchart-services-actions.md](./decision-flowchart-services-actions.md)
- **Copilot向け指示**: [.github/copilot-instructions.md](../.github/copilot-instructions.md)

---

## 🎓 覚えておくべき3つのこと

1. **Services = 技術** | **Actions = ビジネス**
2. **Services = 汎用** | **Actions = 専用**
3. **Services = HOW** | **Actions = WHAT**

---

**印刷推奨**: このファイルをPDFや画像に変換して、手元に置いておくことをお勧めします。

**最終更新**: 2025-11-24
