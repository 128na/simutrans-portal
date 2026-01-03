# エラーハンドリング戦略

Simutrans Portal のバックエンド（Laravel）とフロントエンド（React）におけるエラーハンドリングの包括的な戦略ドキュメントです。

---

## 目次

1. [バックエンド（Laravel）](#バックエンドlaravel)
2. [フロントエンド（React）](#フロントエンドreact)
3. [API 契約](#api-契約)
4. [エラーロギング](#エラーロギング)

---

## バックエンド（Laravel）

### HTTP エラーレスポンス

#### 422 Validation Error（バリデーションエラー）

```php
// Controller
use Illuminate\Validation\ValidationException;

public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'title' => 'required|string',
        'slug' => 'required|unique:articles',
    ]);
    // ...
}

// クライアントには自動的に422レスポンス
```

**レスポンス形式:**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["タイトルは必須です"],
    "slug": ["このスラッグは既に使用されています"]
  }
}
```

#### 401 Unauthorized（認証なし）

```php
// Sanctumミドルウェア
Route::middleware(['auth:sanctum'])->post('/articles', StoreController::class);

// 認証なしでアクセス → 401
```

#### 403 Forbidden（権限なし）

```php
// Policy
class ArticlePolicy
{
    public function update(User $user, Article $article): bool
    {
        return $user->id === $article->user_id || $user->isAdmin();
    }
}

// Controller
public function update(Request $request, Article $article): JsonResponse
{
    $this->authorize('update', $article);  // 403発生
    // ...
}
```

#### 404 Not Found（リソース未検出）

```php
// ルートモデルバインディング
Route::get('articles/{article}', function (Article $article) {
    return $article;  // 見つからなければ404
});

// または
public function show(int $id): Article
{
    return Article::findOrFail($id);  // 見つからなければ404
}
```

#### 500 Server Error（サーバーエラー）

```php
// 予期しない例外
throw new Exception('Something went wrong');

// Laravelが500レスポンスを返す
```

### カスタム例外

```php
<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class InvalidStateException extends Exception
{
    public function __construct(string $message = 'Invalid state')
    {
        parent::__construct($message);
    }

    /**
     * JSON レスポンスに変換
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->message,
            'error' => 'invalid_state',
        ], Response::HTTP_BAD_REQUEST);
    }
}
```

**使用例:**

```php
if ($state !== session('oauth_state')) {
    throw new InvalidStateException('State mismatch in OAuth flow');
}
```

### Exception Handler

```php
// app/Exceptions/Handler.php

use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * 異常をレンダリング
     */
    public function render($request, Throwable $exception)
    {
        // JSON API リクエスト
        if ($request->expectsJson()) {
            return $this->renderJson($exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * JSON エラーレスポンス
     */
    protected function renderJson(Throwable $exception): JsonResponse
    {
        return response()->json([
            'message' => $exception->getMessage(),
            'error_code' => 'server_error',
        ], 500);
    }
}
```

### ロギング

```php
// 重要なイベント
Log::warning('Failed OAuth token refresh', [
    'user_id' => $user->id,
    'provider' => 'twitter',
    'reason' => $exception->getMessage(),
]);

// エラー追跡
Log::error('Article publication failed', [
    'article_id' => $article->id,
    'status_code' => 500,
    'exception' => $exception,
]);
```

---

## フロントエンド（React）

### axios エラーハンドリング

```typescript
import axios from "axios";
import { useErrorHandler } from "@/hooks/useErrorHandler";

// グローバルエラーハンドラー設定
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    const { message, status } = error.response || {};
    console.error(`[${status}] ${message}`);
    return Promise.reject(error);
  }
);
```

### useErrorHandler フック

**用途:** API エラーの一般的なハンドリング（全体通知）

```typescript
// resources/js/state/useAxiosError.ts

import { AxiosError } from "axios";

const useErrorHandler = () => {
  const handleError = (error: AxiosError) => {
    const status = error.response?.status;
    const message = error.response?.data?.message || "エラーが発生しました";

    switch (status) {
      case 422:
        // バリデーションエラー（フォームに表示）
        return error.response?.data?.errors || {};

      case 401:
        // 認証エラー
        window.location.href = "/login";
        return {};

      case 403:
        // 権限エラー
        alert("この操作は実行できません");
        return {};

      case 404:
        // リソース未検出
        alert("見つかりません");
        return {};

      default:
        // その他のエラー
        alert(message);
        return {};
    }
  };

  return { handleError };
};

export default useErrorHandler;
```

**使用例:**

```typescript
const MyComponent = () => {
  const { handleError } = useErrorHandler();

  const fetchData = async () => {
    try {
      const response = await axios.post('/api/v2/articles', data);
      setArticle(response.data);
    } catch (error) {
      handleError(error as AxiosError);
    }
  };

  return <button onClick={fetchData}>保存</button>;
};
```

### useAxiosError フック

**用途:** バリデーションエラーのインライン表示（フォーム別）

```typescript
// resources/js/state/useAxiosError.ts

interface ValidationErrors {
  [key: string]: string[];
}

const useAxiosError = () => {
  const [errors, setErrors] = useState<ValidationErrors>({});

  const handleError = (error: AxiosError): ValidationErrors => {
    if (error.response?.status === 422) {
      const errors = error.response.data?.errors || {};
      setErrors(errors);
      return errors;
    }

    // その他のエラーは別途処理
    console.error(error);
    return {};
  };

  return { errors, handleError, clearErrors: () => setErrors({}) };
};

export default useAxiosError;
```

**使用例:**

```typescript
const ArticleForm = () => {
  const { errors, handleError, clearErrors } = useAxiosError();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    clearErrors();

    try {
      await axios.post('/api/v2/articles', {
        title: formData.title,
        slug: formData.slug,
      });
      setSuccess('記事を作成しました');
    } catch (error) {
      handleError(error as AxiosError);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <input type="text" name="title" />
      {errors.title && <TextError>{errors.title[0]}</TextError>}

      <input type="text" name="slug" />
      {errors.slug && <TextError>{errors.slug[0]}</TextError>}
    </form>
  );
};
```

### logger ユーティリティ

**注意: `console.log` / `console.error` は直接使用禁止**

```typescript
// resources/js/utils/logger.ts

type LogLevel = "info" | "warn" | "error" | "debug";

const logger = {
  info: (message: string, data?: unknown) => {
    console.log(`[INFO] ${message}`, data);
  },

  warn: (message: string, data?: unknown) => {
    console.warn(`[WARN] ${message}`, data);
  },

  error: (message: string, error?: Error | unknown) => {
    console.error(`[ERROR] ${message}`, error);
    // エラー追跡サービスに送信（例: Sentry）
    sendToErrorTracking(message, error);
  },

  debug: (message: string, data?: unknown) => {
    if (process.env.NODE_ENV === "development") {
      console.debug(`[DEBUG] ${message}`, data);
    }
  },
};

export default logger;
```

**使用例:**

```typescript
import logger from "@/utils/logger";

try {
  const data = await fetchArticles();
  logger.info("Articles fetched", { count: data.length });
} catch (error) {
  logger.error("Failed to fetch articles", error);
}
```

### ErrorBoundary コンポーネント

```typescript
// resources/js/components/ErrorBoundary.tsx

import React, { ReactNode } from 'react';
import logger from '@/utils/logger';

interface Props {
  children: ReactNode;
  fallback?: (error: Error) => ReactNode;
}

interface State {
  hasError: boolean;
  error: Error | null;
}

class ErrorBoundary extends React.Component<Props, State> {
  constructor(props: Props) {
    super(props);
    this.state = { hasError: false, error: null };
  }

  static getDerivedStateFromError(error: Error): State {
    return { hasError: true, error };
  }

  componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
    // ログ記録
    logger.error('React error caught', {
      error: error.toString(),
      componentStack: errorInfo.componentStack,
    });
  }

  render() {
    if (this.state.hasError) {
      return (
        this.props.fallback?.(this.state.error!) || (
          <div role="alert">
            <h2>エラーが発生しました</h2>
            <p>ページを再度読み込んでください</p>
            <button onClick={() => window.location.reload()}>
              リロード
            </button>
          </div>
        )
      );
    }

    return this.props.children;
  }
}

export default ErrorBoundary;
```

**使用例:**

```typescript
const ArticleListPage = () => (
  <ErrorBoundary>
    <ArticleList />
  </ErrorBoundary>
);
```

### エラーメッセージコンポーネント

```typescript
// resources/js/components/ui/TextError.tsx

interface Props {
  children: string;
  className?: string;
}

const TextError = ({ children, className }: Props) => (
  <p className={`text-red-600 text-sm ${className || ''}`}>{children}</p>
);

export default TextError;
```

**使用例:**

```typescript
{error && <TextError>{error}</TextError>}
{errors.title && <TextError>{errors.title[0]}</TextError>}
```

---

## API 契約

### リクエスト形式

```
POST /api/v2/articles
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Article Title",
  "slug": "article-slug",
  "status": "publish"
}
```

### レスポンス形式

#### ✅ 成功（200 OK）

```json
{
  "data": {
    "id": 1,
    "title": "Article Title",
    "slug": "article-slug",
    "status": "publish"
  }
}
```

#### ⚠️ バリデーションエラー（422 Unprocessable Entity）

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["タイトルは必須です"],
    "slug": ["このスラッグは既に使用されています"]
  }
}
```

#### 🔐 認証エラー（401 Unauthorized）

```json
{
  "message": "Unauthenticated."
}
```

#### ❌ サーバーエラー（500 Internal Server Error）

```json
{
  "message": "Server error message",
  "error": "server_error"
}
```

---

## エラーロギング

### バックエンド

```php
// ファイル: storage/logs/laravel.log

[2025-01-03 12:34:56] local.ERROR: Article publication failed {
  "article_id": 123,
  "user_id": 456,
  "status_code": 500,
  "message": "Twitter API error"
}
```

### フロントエンド

```
[ERROR] Failed to fetch articles - AxiosError: 500 Internal Server Error
[WARN] Slow API response - took 5000ms
[INFO] Articles fetched - {count: 42}
```

---

## ベストプラクティス

### ✅ 推奨

- [ ] すべての API 呼び出しに try/catch
- [ ] バリデーションエラーはインライン表示
- [ ] サーバーエラーはユーザーフレンドリーなメッセージ
- [ ] console.log は logger を使用
- [ ] エラーは常にログ記録
- [ ] ページはErrorBoundaryでラップ

### ❌ 避けるべき

- `console.log()` 直接使用
- エラーを無視する（`catch` で何もしない）
- ユーザーに技術的エラーメッセージを表示
- 本番環境でスタックトレースを露出
- エラーレスポンスの形式が不統一

---

**最終更新**: 2025-11-24  
**参考ドキュメント**: [README.md - フロントエンド](../../README.md)
