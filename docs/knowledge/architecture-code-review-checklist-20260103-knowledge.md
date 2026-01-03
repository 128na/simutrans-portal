# Services と Actions コードレビューチェックリスト

コードレビュー時に Services と Actions の配置が適切かを確認するためのチェックリストです。

---

## 📋 配置の妥当性チェック

### Service の配置確認

- [ ] 外部API / インフラと通信している？
- [ ] 複数のドメインから再利用される可能性がある？
- [ ] ビジネスロジックが含まれていない？
- [ ] ステートレスである？（またはインスタンス変数が依存注入のみ）
- [ ] ユースケース固有のロジックが含まれていない？

**いずれかで YES の場合、配置は適切です。**

### Action の配置確認

- [ ] 特定のユースケースを表現している？
- [ ] Actionの責務は1つだけ？
- [ ] `__invoke()` メソッドは1つだけ？
- [ ] ビジネスロジックが含まれている？
- [ ] 複数のService/Repositoryを組み合わせている？

**すべて YES の場合、配置は適切です。**

---

## 🚩 レッドフラグ（即座に指摘）

### Services の問題

```php
// ❌ ビジネスロジックが含まれている
class ArticleService {
    public function publish(Article $article): void
    {
        // 公開日時の計算 → ビジネスロジック
        $article->published_at = now();
        $article->save();
    }
}
```

**対応**: ビジネスロジックを `Actions/Article/PublishArticle` に移動

```php
// ❌ ユースケース固有の処理
class UserService {
    public function register(array $data): User
    {
        // 登録フロー全体 → ユースケース
        $user = User::create($data);
        event(new UserRegistered($user));
        return $user;
    }
}
```

**対応**: `Actions/User/Registration` に移動

```php
// ❌ 複数の独立した責務
class ArticleManager {
    public function store() {}   // 記事作成
    public function update() {}  // 記事更新
    public function delete() {}  // 記事削除
}
```

**対応**: 各々を独立した Action に分割

### Actions の問題

```php
// ❌ 複数のメソッド（ユースケース）を持つ
class ArticleAction {
    public function create(array $data): Article { }
    public function update(Article $article, array $data): Article { }
    public function delete(Article $article): void { }
}
```

**対応**: 各々を独立した Action に分割

```php
// ❌ 汎用ロジック（複数ドメインで使える）
class FormatDateAction {
    public function __invoke(DateTime $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
```

**対応**: `Services/DateFormatterService` またはヘルパー関数に変更

```php
// ❌ 外部API呼び出しをそのまま実装
class SendTwitterAction {
    public function __invoke(Article $article): void
    {
        $response = Http::post('https://api.twitter.com/...');
        // APIロジック混在
    }
}
```

**対応**: `Services/Twitter/TwitterV2Api` に委譲

---

## ✅ 良いコード例

### Services の良い例

```php
/**
 * Twitter API v2 ラッパーサービス
 * 外部API通信を隠蔽し、汎用的なインターフェースを提供
 */
class TwitterV2Api
{
    public function __construct(private string $bearerToken) {}

    /**
     * ツイートを投稿（外部通信）
     */
    public function tweet(string $text): string
    {
        $response = Http::withToken($this->bearerToken)
            ->post('https://api.twitter.com/2/tweets', [
                'text' => $text,
            ]);

        return $response->json('data.id');
    }
}
```

**ポイント**:

- ✅ 外部API通信に特化
- ✅ 汎用的（複数ユースケースから利用可能）
- ✅ ビジネスロジックなし
- ✅ ステートレス

### Actions の良い例

```php
/**
 * 記事をTwitterに投稿する
 * 特定のユースケース（記事公開時のSNS通知）
 */
class ToTwitter
{
    public function __construct(
        private TwitterV2Api $twitter,
        private ArticleRepository $articles,
    ) {}

    public function __invoke(Article $article): void
    {
        // ビジネスロジック: ツイート文の構築
        $text = sprintf(
            '%s\n%s/articles/%s',
            $article->title,
            config('app.url'),
            $article->slug,
        );

        // Service を使って外部通信
        $this->twitter->tweet($text);

        // 記録を保存
        $article->update(['twitter_posted_at' => now()]);
    }
}
```

**ポイント**:

- ✅ 特定のユースケース（記事→Twitter投稿）
- ✅ ビジネスルール（ツイート文の構築）
- ✅ Serviceを組み合わせ
- ✅ ユースケース完結

---

## 📝 命名のチェック

### Services の命名確認

```php
// ✅ Good: {機能}Service または {サービス}ApiClient
MarkdownService
TwitterV2Api
FileInfoService
BlueSkyApiClient

// ⚠️ 要確認: {動詞}Action → Services に混入していないか？
PublishService         // → Actions/Article/PublishArticle?
ConvertService         // → Actions/Article/ConvertArticle?

// ❌ Bad: 曖昧な名前
Helper, Util, Manager  // 役割不明確
```

### Actions の命名確認

```php
// ✅ Good: {動詞}{対象} で始まる
StoreArticle
UpdateProfile
SendInvite
PublishReservation

// ⚠️ 要確認: {対象}Service → Actions に混入していないか？
ArticlePublisher       // → Actions/Article/PublishArticle?
UserManager            // → Actions/User/* に分割?

// ❌ Bad: 動詞がない、役割不明確
Article, Publication   // 名詞のみ
ArticleHandler         // ハンドラー？
```

---

## 🧪 テストのチェック

### Services のテスト確認

- [ ] `tests/Unit/Services/` に配置されている？
- [ ] 外部依存がモック化されている？
- [ ] 入力→出力を検証している？
- [ ] エラーハンドリングのテストがある？

```php
// ✅ Good: 外部APIをモック
class MarkdownServiceTest extends TestCase
{
    public function test_converts_markdown_to_html(): void
    {
        $service = new MarkdownService();
        $html = $service->toHtml('# Title');

        $this->assertStringContainsString('<h1>Title</h1>', $html);
    }
}
```

### Actions のテスト確認

- [ ] `tests/Feature/Actions/` に配置されている？
- [ ] ビジネスロジックを検証している？
- [ ] データベース操作をテストしている？
- [ ] イベント発火などの副作用をテストしている？

```php
// ✅ Good: ビジネスロジック + 副作用を検証
class StoreArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_記事を作成できる(): void
    {
        $user = User::factory()->create();
        $data = ['title' => 'Test', 'status' => 'publish'];

        $article = app(StoreArticle::class)($user, $data);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'user_id' => $user->id,
            'title' => 'Test',
        ]);
    }
}
```

---

## 🔗 依存関係のチェック

### Services の依存関係

```php
// ✅ OK: インフラ層への依存
class FileInfoService
{
    public function __construct(
        private Filesystem $filesystem,  // ✅ インフラ
        private ImageResizer $resizer,   // ✅ 他の Service
    ) {}
}

// ❌ NG: Repository/Action への依存
class TwitterService
{
    public function __construct(
        private ArticleRepository $repo,  // ❌ データアクセスはAction側で
        private StoreArticle $action,     // ❌ Action に依存
    ) {}
}
```

### Actions の依存関係

```php
// ✅ OK: Service/Repository への依存
class StoreArticle
{
    public function __construct(
        private ArticleRepository $repository,      // ✅ Repository
        private MarkdownService $markdown,          // ✅ Service
        private DecidePublishedAt $decidePublishedAt, // ✅ 他の Action
    ) {}
}

// ⚠️ 注意: HTTP リクエストへの直接依存
class UpdateArticle
{
    public function __invoke(Article $article, Request $request): Article
    {
        // ❌ Request を直接使う → Controller で処理してから渡す
        // ✅ 配列データとして受け取る
    }
}
```

---

## 📌 レビューコメント テンプレート

### 配置が不適切な場合

````markdown
### 🚀 配置の改善提案

このクラスは **Service** から **Action** に移動すべきだと思います。

**理由:**

- ビジネスロジック（〇〇の計算）が含まれている
- 複数のRepository/Serviceを組み合わせている
- 特定のユースケース（記事作成フロー）を表現している

**提案:**

```php
// Before: Services/ArticleService
// After: Actions/Article/StoreArticle
```
````

移動後は以下を確認してください:

- [ ] クラス名を動詞で始める（`StoreArticle`）
- [ ] `__invoke()` メソッドは1つだけ
- [ ] 関連するテストを `tests/Feature/Actions/` に移動

````

### 命名が曖昧な場合

```markdown
### 💬 命名の改善提案

このクラスの役割をより明確にするため、命名を改善すると良いと思います。

**現在の名前:** `ArticleManager`
**提案:** `StoreArticle` または `UpdateArticle`

**理由:**
- `Manager` は複数の責務を示唆するため避ける
- 動詞を先頭にすることで、実行アクションが明確になる

参考: [Services/Actions ガイドライン](../docs/architecture-services-actions-20260103-knowledge.md)
````

### テストの不足

```markdown
### 🧪 テストの追加をお願いします

このActionにはテストが不足しているようです。

**追加をお願いしたいテスト:**

- [ ] 正常系: 〇〇が作成される
- [ ] エラー系: △△の場合は例外を投げる
- [ ] 副作用: イベント `UserRegistered` が発火する

参考: [テスト実装例](../tests/Unit/Services/Twitter/README.md)
```

---

## 🎓 チェックリスト（PR マージ前）

レビュアー向け最終確認リスト:

### 配置

- [ ] Service は外部依存 / 汎用機能のみ？
- [ ] Action はビジネスロジック / ユースケース実装のみ？
- [ ] 複数の責務を持つクラスが混在していない？

### 命名

- [ ] Services: `{機能}Service` 形式？
- [ ] Actions: 動詞で始める形式？
- [ ] 役割が名前から明確に判断できる？

### テスト

- [ ] Unit/Feature テストが適切に配置？
- [ ] 外部依存がモック化されている？
- [ ] ビジネスロジックが検証されている？

### ドキュメント

- [ ] 複雑なロジックはコメントされている？
- [ ] PHPdoc が記述されている？
- [ ] README が更新されている（新規ファイル）？

---

**最終更新**: 2025-11-24  
**参考ドキュメント**: [Services/Actions 完全ガイド](./architecture-services-actions-20260103-knowledge.md)
