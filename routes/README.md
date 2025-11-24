# Routes ディレクトリ

ルーティング定義を配置します。

## ファイル一覧

```
routes/
├── web.php          # 公開Webページ（フロント）
├── api.php          # API v2（Sanctum認証必須）
├── internal_api.php # 内部API（CSRF検証のみ）
├── channels.php     # ブロードキャストチャンネル
└── console.php      # Artisanコマンド
```

## web.php（公開Webページ）

### 概要

- **認証**: 不要（一部除く）
- **ミドルウェア**: `web`
- **セッション**: 有効
- **CSRF保護**: 有効

### ルート構造

```php
// トップページ
Route::get('/', TopController::class)->name('front.index');

// Pak別一覧
Route::prefix('pak')->group(function () {
    Route::get('{slug}', PakController::class)->name('front.pak');
});

// 検索
Route::prefix('search')->name('front.search.')->group(function () {
    Route::get('/', IndexController::class)->name('index');
});

// お知らせ
Route::prefix('announces')->name('front.announces.')->group(function () {
    Route::get('/', IndexController::class)->name('index');
});

// タグ
Route::prefix('tags')->name('front.tags.')->group(function () {
    Route::get('/', IndexController::class)->name('index');
    Route::get('{tag:name}', ShowController::class)->name('show');
});

// カテゴリ
Route::prefix('categories')->name('front.categories.')->group(function () {
    Route::get('/', IndexController::class)->name('index');
    Route::get('{category:slug}', ShowController::class)->name('show');
});

// ユーザー
Route::prefix('users')->name('front.users.')->group(function () {
    Route::get('/', IndexController::class)->name('index');
    Route::get('{user:id}', ShowController::class)->name('show');
});

// 認証（Fortify）
Route::prefix('login')->middleware('guest')->group(function () {
    Route::get('/', LoginController::class)->name('login');
    Route::get('two-factor', TwoFactorController::class)->name('two-factor.login');
});

Route::prefix('register')->middleware('guest')->group(function () {
    Route::get('/', RegisterController::class)->name('register');
    Route::get('invite/{user}', InviteController::class)->name('register.invite');
});

// マイページ（認証必須）
Route::prefix('mypage')->middleware(['auth', 'verified'])->name('mypage.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', IndexController::class)->name('index');
        Route::get('create', CreateController::class)->name('create');
        Route::get('{article}/edit', EditController::class)->name('edit');
    });

    Route::get('attachments', AttachmentController::class)->name('attachments');
    Route::get('tags', TagController::class)->name('tags');
    Route::get('profile', ProfileController::class)->name('profile');
    Route::get('analytics', AnalyticsController::class)->name('analytics');
});

// 記事詳細・ダウンロード
Route::prefix('articles')->name('front.articles.')->group(function () {
    Route::get('{slug}', ShowController::class)->name('show');
    Route::get('{article}/download', DownloadController::class)->name('download');
    Route::get('{article}/conversion', ConversionController::class)->name('conversion');
});

// フォールバック（404）
Route::fallback(FallbackController::class);
```

### 命名規則

- **Prefix**: `front.{機能}.{アクション}`
- **例**: `front.articles.show`, `front.tags.index`

## api.php（API v2）

### 概要

- **認証**: Laravel Sanctum必須
- **ミドルウェア**: `api`, `auth:sanctum`
- **レスポンス**: JSON
- **CSRF保護**: 不要（Bearerトークン認証）

### ルート構造

```php
Route::prefix('v2')->middleware(['auth:sanctum'])->group(function () {

    // タグ
    Route::post('tags', StoreController::class);
    Route::post('tags/{tag}', UpdateController::class);

    // 添付ファイル
    Route::post('attachments', StoreController::class);
    Route::delete('attachments/{attachment}', DestroyController::class);

    // 記事
    Route::post('articles', StoreController::class);
    Route::post('articles/{article}', UpdateController::class);

    // プロフィール
    Route::post('profile', UpdateController::class);

    // アナリティクス
    Route::post('analytics', ShowController::class);
});
```

### 認証

```javascript
// フロントエンドからの呼び出し
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.withCredentials = true;

// Sanctum CSRFトークン取得
await axios.get("/sanctum/csrf-cookie");

// API呼び出し
const response = await axios.post("/api/v2/articles", articleData);
```

### レスポンス形式

```json
{
  "data": {
    "id": 1,
    "title": "記事タイトル",
    "slug": "article-slug"
  }
}
```

```json
{
  "errors": {
    "title": ["タイトルは必須です"],
    "slug": ["このスラッグは既に使用されています"]
  }
}
```

## internal_api.php（内部API）

### 概要

- **認証**: CSRFトークン検証のみ
- **用途**: フロント↔バックエンド間の通信
- **ミドルウェア**: `web`

### ルート構造

```php
Route::middleware(['web'])->group(function () {

    // 記事閲覧数カウント
    Route::post('articles/{article}/views', IncrementViewController::class);

    // ダウンロード数カウント
    Route::post('articles/{article}/conversions', IncrementConversionController::class);

    // リダイレクト記録
    Route::get('redirects/{redirect}', RedirectController::class);
});
```

## channels.php（ブロードキャスト）

### 概要

- **用途**: Laravel Echo / Pusher チャンネル認証
- **認証**: ユーザー固有のプライベートチャンネル

### チャンネル定義

```php
use Illuminate\Support\Facades\Broadcast;

// ユーザー個別通知チャンネル
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// 記事編集リアルタイム通知
Broadcast::channel('article.{articleId}', function ($user, $articleId) {
    return $user->can('update', Article::find($articleId));
});
```

## console.php（Artisanコマンド）

### 概要

- **用途**: カスタムArtisanコマンドの定義
- **スケジュール**: コマンドスケジューリング

### コマンド定義

```php
use Illuminate\Support\Facades\Schedule;

// 定期実行スケジュール
Schedule::command('article:check-dead-links')->daily();
Schedule::command('article:update-search-index')->hourly();
Schedule::command('backup:clean')->daily()->at('02:00');
```

### カスタムクロージャコマンド

```php
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
```

## ルート設計の原則

### RESTful設計

| HTTP Method | URI                        | Action  | Route Name       |
| ----------- | -------------------------- | ------- | ---------------- |
| GET         | `/articles`                | index   | articles.index   |
| GET         | `/articles/create`         | create  | articles.create  |
| POST        | `/articles`                | store   | articles.store   |
| GET         | `/articles/{article}`      | show    | articles.show    |
| GET         | `/articles/{article}/edit` | edit    | articles.edit    |
| PUT/PATCH   | `/articles/{article}`      | update  | articles.update  |
| DELETE      | `/articles/{article}`      | destroy | articles.destroy |

### ルートモデルバインディング

```php
// 自動解決
Route::get('articles/{article}', function (Article $article) {
    return $article;
});

// カスタムキー
Route::get('articles/{article:slug}', function (Article $article) {
    return $article;
});

// スコープバインディング
Route::get('users/{user}/articles/{article:slug}', function (User $user, Article $article) {
    return $article;
})->scopeBindings();
```

### ミドルウェアグループ

```php
Route::middleware(['auth', 'verified'])->group(function () {
    // 認証＋メール認証必須
});

Route::middleware(['auth', 'admin'])->group(function () {
    // 管理者のみ
});
```

## ルート確認コマンド

```bash
# すべてのルート一覧
php artisan route:list

# 特定のルートのみ
php artisan route:list --path=mypage

# ミドルウェア付き表示
php artisan route:list --except-vendor

# JSON形式
php artisan route:list --json
```

## ベストプラクティス

### ✅ 推奨

- ルート名を必ず付ける（`->name('front.articles.show')`）
- Prefixでグループ化
- Controllerは単一アクション（`__invoke()`）
- ルートモデルバインディングを活用

### ❌ 避けるべき

- クロージャにビジネスロジックを書く
- ルート名なしでURLハードコード
- ネストが深すぎる（3階層まで）

## 関連ドキュメント

- **[Controllers](../app/Http/Controllers/README.md)** - コントローラー構造
- **[API Documentation](../app/OpenApi/README.md)** - OpenAPI仕様
- **[README.md](../README.md)** - プロジェクト概要
