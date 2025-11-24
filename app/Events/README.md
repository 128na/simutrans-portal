# Events & Listeners ディレクトリ

イベント駆動アーキテクチャを実装したクラスを配置します。

## ディレクトリ構造

```
Events/
├── Article/
│   ├── ArticleCreated.php        # 記事作成イベント
│   ├── ArticleDeleted.php        # 記事削除イベント
│   ├── ArticlePublished.php      # 記事公開イベント
│   └── ArticleUpdated.php        # 記事更新イベント
├── Discord/
│   └── DiscordInvited.php        # Discord招待イベント
├── Tag/
│   └── TagCreated.php            # タグ作成イベント
├── User/
│   ├── UserCreated.php           # ユーザー作成イベント
│   ├── UserLoggedIn.php          # ログインイベント
│   └── UserRegistered.php        # ユーザー登録イベント
├── ArticleConversion.php         # ダウンロードイベント
└── ArticleShown.php              # 記事閲覧イベント

Listeners/
├── Article/
│   ├── OnArticleCreated.php      # 記事作成リスナー
│   ├── OnArticleDeleted.php      # 記事削除リスナー
│   ├── OnArticlePublished.php    # 記事公開リスナー
│   └── OnArticleUpdated.php      # 記事更新リスナー
├── Discord/
│   └── OnDiscordInvited.php      # Discord招待リスナー
├── Tag/
│   └── OnTagCreated.php          # タグ作成リスナー
└── User/
    ├── OnLogin.php               # ログインリスナー
    ├── OnRegistered.php          # 登録リスナー
    └── OnUserCreated.php         # ユーザー作成リスナー
```

## イベント駆動アーキテクチャとは

イベントの発生（Event）とそれに対する処理（Listener）を分離することで、疎結合なシステムを実現します。

### メリット

- **疎結合**: イベント発火側はリスナーを知らなくて良い
- **拡張性**: 新しいリスナーを追加しやすい
- **テスタビリティ**: イベントとリスナーを個別にテスト可能
- **保守性**: 処理の追加・削除が容易

## 主要イベントフロー

### 記事公開フロー

```
Controller
  ↓ Action実行
StoreArticle (Action)
  ↓ event()
ArticlePublished (Event)
  ↓
├─ OnArticlePublished (Listener)
│   ├─ Discord通知
│   ├─ Twitter投稿
│   ├─ BlueSky投稿
│   └─ Misskey投稿
└─ JobUpdateSearchIndex (Job)
    └─ 検索インデックス更新
```

### ユーザー登録フロー

```
RegisterController
  ↓
CreateNewUser (Action)
  ↓ event()
UserRegistered (Event)
  ↓
OnRegistered (Listener)
  ├─ ウェルカムメール送信
  ├─ 初期設定作成
  └─ Discord通知
```

### ログインフロー

```
LoginController
  ↓ event()
UserLoggedIn (Event)
  ↓
OnLogin (Listener)
  └─ ログイン履歴記録
```

## 実装パターン

### Event（イベント）

```php
<?php

declare(strict_types=1);

namespace App\Events\Article;

use App\Models\Article;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArticlePublished
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * コンストラクタ
     */
    public function __construct(
        public Article $article,
    ) {}
}
```

### Listener（リスナー）

```php
<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\ArticlePublished;
use App\Actions\SendSNS\Article\ToTwitter;
use App\Actions\SendSNS\Article\ToDiscord;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnArticlePublished implements ShouldQueue
{
    /**
     * キュー名
     */
    public string $queue = 'notifications';

    /**
     * リスナーを実行
     */
    public function handle(
        ArticlePublished $event,
        ToTwitter $toTwitter,
        ToDiscord $toDiscord,
    ): void {
        // Twitter投稿
        ($toTwitter)($event->article);

        // Discord通知
        ($toDiscord)($event->article);
    }

    /**
     * 失敗時の処理
     */
    public function failed(ArticlePublished $event, Throwable $exception): void
    {
        Log::error('Failed to send SNS notification', [
            'article_id' => $event->article->id,
            'exception' => $exception->getMessage(),
        ]);
    }
}
```

### イベント登録

`app/Providers/EventServiceProvider.php` でイベントとリスナーを紐付けます。

```php
protected $listen = [
    // 記事公開
    ArticlePublished::class => [
        OnArticlePublished::class,
    ],

    // 記事作成
    ArticleCreated::class => [
        OnArticleCreated::class,
    ],

    // ユーザー登録
    UserRegistered::class => [
        OnRegistered::class,
    ],
];
```

または、自動検出を有効化:

```php
public function shouldDiscoverEvents(): bool
{
    return true;
}
```

## イベント発火

### event()ヘルパー

```php
use App\Events\Article\ArticlePublished;

// イベント発火
event(new ArticlePublished($article));
```

### モデルイベント

Eloquent Modelのライフサイクルイベントを使用できます。

```php
class Article extends Model
{
    protected $dispatchesEvents = [
        'created' => ArticleCreated::class,
        'updated' => ArticleUpdated::class,
        'deleted' => ArticleDeleted::class,
    ];
}
```

または、Observer を使用:

```php
// app/Providers/EventServiceProvider.php
public function boot(): void
{
    Article::observe(ArticleObserver::class);
}

// app/Observers/ArticleObserver.php
class ArticleObserver
{
    public function created(Article $article): void
    {
        event(new ArticleCreated($article));
    }
}
```

## キューでの非同期実行

### Listenerを非同期化

```php
use Illuminate\Contracts\Queue\ShouldQueue;

class OnArticlePublished implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * キュー名
     */
    public string $queue = 'notifications';

    /**
     * リトライ回数
     */
    public int $tries = 3;

    /**
     * タイムアウト（秒）
     */
    public int $timeout = 120;
}
```

### 条件付きキュー実行

```php
class OnArticlePublished implements ShouldQueue
{
    /**
     * キューで実行するか判定
     */
    public function shouldQueue(ArticlePublished $event): bool
    {
        return $event->article->status === ArticleStatus::Publish;
    }
}
```

## ブロードキャストイベント

リアルタイム通知にはブロードキャストイベントを使用します。

```php
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ArticlePublished implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Article $article,
    ) {}

    /**
     * ブロードキャストチャンネル
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('articles'),
            new PrivateChannel('user.' . $this->article->user_id),
        ];
    }

    /**
     * ブロードキャストデータ
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->article->id,
            'title' => $this->article->title,
            'slug' => $this->article->slug,
        ];
    }
}
```

フロントエンド（Laravel Echo）:

```javascript
Echo.channel("articles").listen("ArticlePublished", (e) => {
  console.log("New article:", e.title);
});
```

## 主要イベント・リスナー

### Article/ArticlePublished

**トリガー**: 記事が公開されたとき

**リスナー**:

- `OnArticlePublished` - SNS通知（Twitter, Discord, BlueSky, Misskey）
- `JobUpdateSearchIndex` - 検索インデックス更新

### Article/ArticleCreated

**トリガー**: 記事が作成されたとき

**リスナー**:

- `OnArticleCreated` - 初期設定（閲覧数カウンター作成等）

### Article/ArticleUpdated

**トリガー**: 記事が更新されたとき

**リスナー**:

- `OnArticleUpdated` - 検索インデックス更新、キャッシュクリア

### Article/ArticleDeleted

**トリガー**: 記事が削除されたとき

**リスナー**:

- `OnArticleDeleted` - 関連データ削除、ストレージクリーンアップ

### User/UserRegistered

**トリガー**: ユーザーが登録されたとき

**リスナー**:

- `OnRegistered` - ウェルカムメール、初期設定、Discord通知

### User/UserLoggedIn

**トリガー**: ユーザーがログインしたとき

**リスナー**:

- `OnLogin` - ログイン履歴記録、最終ログイン日時更新

### ArticleShown

**トリガー**: 記事が閲覧されたとき

**リスナー**:

- 閲覧数カウンター更新

### ArticleConversion

**トリガー**: 添付ファイルがダウンロードされたとき

**リスナー**:

- ダウンロード数カウンター更新

## テスト

### イベントのテスト

```php
use Illuminate\Support\Facades\Event;

final class ArticlePublishedTest extends TestCase
{
    public function test_event_is_dispatched(): void
    {
        Event::fake();

        $article = Article::factory()->create();

        event(new ArticlePublished($article));

        Event::assertDispatched(ArticlePublished::class, function ($event) use ($article) {
            return $event->article->id === $article->id;
        });
    }
}
```

### リスナーのテスト

```php
use Illuminate\Support\Facades\Notification;

final class OnArticlePublishedTest extends TestCase
{
    public function test_listener_sends_notifications(): void
    {
        Notification::fake();

        $article = Article::factory()->create();
        $event = new ArticlePublished($article);
        $listener = new OnArticlePublished();

        $listener->handle($event, app(ToDiscord::class));

        // Discord通知が送信されたことを検証
    }
}
```

## ベストプラクティス

### ✅ 推奨

- イベントは不変（イミュータブル）にする
- リスナーは小さく単一責任
- 長時間処理はキューで非同期化
- 失敗時の処理（`failed()`）を実装
- イベント名は過去形（`ArticlePublished`, `UserRegistered`）

### ❌ 避けるべき

- イベント内にビジネスロジック
- リスナー間の依存関係
- 同期実行での重い処理
- 無限ループ（イベント→リスナー→イベント）

## イベント vs Job

| 項目         | Event/Listener       | Job                  |
| ------------ | -------------------- | -------------------- |
| **用途**     | アクション完了の通知 | タスクの実行         |
| **トリガー** | ビジネスイベント     | 明示的なディスパッチ |
| **複数処理** | 複数リスナー         | 単一ジョブ           |
| **疎結合**   | 高い                 | 中程度               |
| **例**       | 記事公開→SNS通知     | 画像変換             |

## 関連ドキュメント

- **[Jobs](../Jobs/README.md)** - キュージョブ
- **[Actions](../Actions/README.md)** - ビジネスロジック
- **[README.md](../../README.md)** - プロジェクト概要
