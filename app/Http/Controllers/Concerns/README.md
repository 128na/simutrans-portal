# Controller Concerns - RespondsWithJson

## 概要

`RespondsWithJson` トレイトは、コントローラーで HTML と JSON の両方のレスポンスを簡単に返せるようにする汎用的な機能です。

`.json` 拡張子または `Accept: application/json` ヘッダーに基づいて、自動的に適切なレスポンスを返します。

## 使い方

### 1. トレイトをインポート

```php
use App\Http\Controllers\Concerns\RespondsWithJson;

final class YourController extends Controller
{
    use RespondsWithJson;

    // ...
}
```

### 2. メソッドでトレイトを使用

#### パターン A: JsonResource を返す場合

```php
public function show(string $slug, Request $request): JsonResponse|View
{
    // .json拡張子を削除
    $slug = $this->removeJsonExtension($slug);

    $article = $this->repository->findBySlug($slug);

    // JSON/HTML両方に対応したレスポンスを返す
    return $this->respondWithJson(
        $request,
        new ArticleShow($article),  // JsonResource
        'pages.articles.show',      // Bladeビュー
        ['meta' => $this->metaService->article($article)]  // 追加のビューデータ
    );
}
```

#### パターン B: 配列またはコレクションを返す場合

```php
public function users(Request $request): JsonResponse|View
{
    $users = $this->repository->getForList();

    return $this->respondWithJson(
        $request,
        $users,  // 配列またはコレクション
        'pages.users.index',
        ['users' => $users, 'meta' => $this->metaService->users()]
    );
}
```

#### パターン C: 複数のデータを返す場合

```php
public function user(string $userIdOrNickname, Request $request): JsonResponse|View
{
    $userIdOrNickname = $this->removeJsonExtension($userIdOrNickname);
    $user = $this->repository->findOrFail($userIdOrNickname);
    $articles = ArticleList::collection($this->articleRepository->getByUser($user->id));

    return $this->respondWithJson(
        $request,
        ['user' => $user, 'articles' => $articles],  // 連想配列
        'pages.users.show',
        ['user' => $user, 'articles' => $articles, 'meta' => $this->metaService->user($user)]
    );
}
```

### 3. ルートの設定

`.json` 拡張子を許可するために、ルートに `->where()` を追加します：

```php
Route::get('/users/{userIdOrNickname}', [UserController::class, 'user'])
    ->name('users.show')
    ->where('userIdOrNickname', '.*');  // .json拡張子を含む任意の文字列を許可
```

### 4. テストの追加

```php
public function test_show_json_response(): void
{
    $article = Article::factory()->create();

    // .json拡張子でJSONレスポンス
    $response = $this->get("/articles/{$article->slug}.json");
    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json')
        ->assertJsonStructure(['id', 'title', 'slug']);

    // Accept: application/jsonヘッダーでJSONレスポンス
    $response = $this->get("/articles/{$article->slug}", ['Accept' => 'application/json']);
    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json')
        ->assertJsonStructure(['id', 'title', 'slug']);
}
```

## メソッド一覧

### `wantsJson(Request $request): bool`

リクエストが JSON レスポンスを期待しているかチェックします。

- `.json` 拡張子がある場合
- `Accept: application/json` ヘッダーがある場合

に `true` を返します。

### `hasJsonExtension(Request $request): bool`

リクエストパスに `.json` 拡張子が含まれているかチェックします。

### `removeJsonExtension(string $value): string`

文字列から `.json` 拡張子を削除します。

```php
$slug = $this->removeJsonExtension('my-article.json'); // 'my-article'
```

### `respondWithJson(Request $request, mixed $data, string $view, array $viewData = []): JsonResponse|View`

JSON または HTML のレスポンスを返します。

**引数:**

- `$request` - HTTPリクエスト
- `$data` - JSONレスポンスのデータ（JsonResource、配列、Responsable）
- `$view` - Bladeビューのパス
- `$viewData` - ビューに渡す追加データ（オプション）

**戻り値:**

- JSON が期待される場合: `JsonResponse`
- それ以外: `View`

### `jsonResponse(mixed $data): JsonResponse`

データから JSON レスポンスを作成します。

サポートする型:

- `JsonResource` - `$resource->response()->getData()`
- `Responsable` - `$responsable->toResponse()->getData()`
- 配列・コレクション - そのまま JSON 化

### `getViewDataKey(mixed $data): array`

JsonResource から自動的にビュー変数名を推測します。

```php
new ArticleShow($article) → ['article' => $article]
ArticleList::collection($articles) → ['articles' => $articles]
```

## 実装例

### ShowController（記事詳細）

```php
final class ShowController extends Controller
{
    use RespondsWithJson;

    public function show(User $user, string $articleSlug, Request $request): JsonResponse|View
    {
        $articleSlug = $this->removeJsonExtension($articleSlug);
        $article = $this->getForShow->__invoke($user, $articleSlug);

        return $this->respondWithJson(
            $request,
            new ArticleShow($article),
            'pages.show.show',
            ['meta' => $this->metaOgpService->frontArticle($article)]
        );
    }
}
```

### UserController（ユーザー一覧・詳細）

```php
final class UserController extends Controller
{
    use RespondsWithJson;

    public function users(Request $request): JsonResponse|View
    {
        $users = $this->userRepository->getForList();

        return $this->respondWithJson(
            $request,
            $users,
            'pages.users.index',
            ['users' => $users, 'meta' => $this->metaOgpService->frontUsers()]
        );
    }

    public function user(string $userIdOrNickname, Request $request): JsonResponse|View
    {
        $userIdOrNickname = $this->removeJsonExtension($userIdOrNickname);
        $user = $this->userRepository->firstOrFailByIdOrNickname($userIdOrNickname);
        $articles = ArticleList::collection($this->articleRepository->getByUser($user->id));

        return $this->respondWithJson(
            $request,
            ['user' => $user, 'articles' => $articles],
            'pages.users.show',
            ['user' => $user, 'articles' => $articles, 'meta' => $this->metaOgpService->frontUser($user)]
        );
    }
}
```

## 利点

### コードの簡潔化

**Before（25行）:**

```php
public function show(User $user, string $articleSlug, Request $request): JsonResponse|View
{
    if (str_ends_with($articleSlug, '.json')) {
        $articleSlug = substr($articleSlug, 0, -5);
    }

    $article = $this->getForShow->__invoke($user, $articleSlug);

    if ($this->wantsJson($request)) {
        $resource = new ArticleShow($article);
        return response()->json($resource->response()->getData());
    }

    return view('pages.show.show', [
        'article' => $article,
        'meta' => $this->metaOgpService->frontArticle($article),
    ]);
}
```

**After（12行）:**

```php
public function show(User $user, string $articleSlug, Request $request): JsonResponse|View
{
    $articleSlug = $this->removeJsonExtension($articleSlug);
    $article = $this->getForShow->__invoke($user, $articleSlug);

    return $this->respondWithJson(
        $request,
        new ArticleShow($article),
        'pages.show.show',
        ['meta' => $this->metaOgpService->frontArticle($article)]
    );
}
```

### 一貫性

すべてのコントローラーで同じパターンで JSON 対応が可能です。

### テストしやすさ

トレイトのメソッドは個別にテスト可能で、コントローラーのテストもシンプルになります。

### 拡張性

将来的に JSON レスポンスの形式を変更する場合、トレイトのみを修正すればすべてのコントローラーに反映されます。

## 制限事項

### パラメータの順序

`.json` 拡張子を含むパラメータは、ルート定義の**最後**にある必要があります。

**OK:**

```php
Route::get('/users/{userIdOrNickname}', ...)->where('userIdOrNickname', '.*');
Route::get('/users/{userIdOrNickname}/{articleSlug}', ...)->where('articleSlug', '.*');
```

**NG:**

```php
// articleSlugの後にパラメータがある場合、.jsonが正しく解釈されない
Route::get('/users/{articleSlug}/{action}', ...)->where('articleSlug', '.*');
```

### 一覧ページの.json拡張子

一覧ページ（`/users`など）で `.json` 拡張子を使う場合、ルート定義が複雑になります。

推奨: 一覧ページは `Accept` ヘッダーのみで JSON 対応し、`.json` 拡張子は詳細ページのみで使用する。

## 関連ドキュメント

- [README.md](../../../../README.md) - プロジェクト概要
- [Controllers README](../README.md) - コントローラー構造
- [Resources](../../Resources/README.md) - JsonResource の使い方
