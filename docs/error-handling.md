# エラーハンドリングガイドライン

このドキュメントは、フロントエンドにおけるエラーハンドリングの統一的なアプローチを説明します。

## 概要

エラー処理の一貫性を保つため、以下の仕組みを導入しています：

1. **`errorHandler.ts`** - 統一されたエラー処理ユーティリティ
2. **`ErrorBoundary`** - React のエラーバウンダリーコンポーネント
3. **`useErrorHandler`** - エラー処理用のカスタムフック

## 基本的な使用方法

### 1. API 呼び出し時のエラー処理

```typescript
import axios from "axios";
import { isValidationError } from "@/lib/errorHandler";
import { useErrorHandler } from "@/hooks/useErrorHandler";
import { useAxiosError } from "@/hooks/useAxiosError";

const MyComponent = () => {
  const { setError } = useAxiosError();
  const { handleErrorWithContext } = useErrorHandler({
    component: "MyComponent",
  });

  const save = async () => {
    try {
      await axios.post("/api/endpoint", data);
    } catch (error) {
      // バリデーションエラー（422）の場合はフォームにエラーを表示
      if (isValidationError(error)) {
        setError(error.response?.data);
      } else {
        // その他のエラーはユーザーに通知
        handleErrorWithContext(error, { action: "save" });
      }
    }
  };

  return <button onClick={save}>保存</button>;
};
```

### 2. シンプルなエラー処理

```typescript
import { handleError } from "@/lib/errorHandler";

const handleDelete = async (id: number) => {
  try {
    await axios.delete(`/api/items/${id}`);
  } catch (error) {
    handleError(error, { component: "ItemList", action: "delete" });
  }
};
```

### 3. サイレントエラー（ユーザー通知なし）

```typescript
import { handleErrorSilent } from "@/lib/errorHandler";

const fetchData = async () => {
  try {
    const res = await axios.get("/api/data");
    return res.data;
  } catch (error) {
    // エラーはログに記録されるが、ユーザーには通知しない
    handleErrorSilent(error, { component: "DataFetcher", action: "fetch" });
    return null;
  }
};
```

## エラーバウンダリーの使用

すべてのページコンポーネントは `ErrorBoundary` でラップされています。
これにより、レンダリングエラーが発生した場合もアプリ全体がクラッシュせず、フォールバック UI が表示されます。

```typescript
import { createRoot } from "react-dom/client";
import { ErrorBoundary } from "@/components/ErrorBoundary";

createRoot(app).render(
  <ErrorBoundary name="MyPage">
    <App />
  </ErrorBoundary>
);
```

### カスタムフォールバック UI

```typescript
<ErrorBoundary
  name="MyComponent"
  fallback={<div>カスタムエラーメッセージ</div>}
>
  <MyComponent />
</ErrorBoundary>
```

## API リファレンス

### `errorHandler.ts`

| 関数/クラス            | 説明                                       |
| ---------------------- | ------------------------------------------ |
| `handleError`          | エラーをログに記録し、ユーザーに通知する   |
| `handleErrorSilent`    | エラーをログに記録するが、通知はしない     |
| `extractErrorMessage`  | エラーからユーザー向けメッセージを抽出する |
| `isAxiosError`         | Axios エラーかどうかを判定する             |
| `isValidationError`    | バリデーションエラー（422）かどうかを判定  |
| `AppError`             | アプリケーション固有のエラークラス         |

### `useErrorHandler`

```typescript
const {
  handleErrorWithContext, // エラーをコンテキスト付きで処理（通知あり）
  handleSilent, // エラーをサイレントに処理（通知なし）
  getMessage, // エラーからメッセージを取得
  isValidation, // バリデーションエラーかどうかを判定
} = useErrorHandler({ component: "MyComponent" });
```

### `ErrorBoundary`

| Props     | 型        | 説明                                 |
| --------- | --------- | ------------------------------------ |
| children  | ReactNode | 子コンポーネント                     |
| fallback  | ReactNode | エラー時に表示するフォールバック UI  |
| name      | string    | コンポーネント識別用の名前           |

## エラーメッセージの自動抽出

`extractErrorMessage` は以下の順序でメッセージを抽出します：

1. `AppError` の場合：エラーメッセージをそのまま使用
2. `AxiosError` の場合：
   - 422（バリデーション）：`response.data.message` または「入力内容に問題があります」
   - ネットワークエラー：「ネットワークエラーが発生しました。接続を確認してください」
   - サーバーメッセージがある場合：`response.data.message`
   - 401：「認証が必要です。ログインしてください」
   - 403：「この操作を行う権限がありません」
   - 404：「リソースが見つかりませんでした」
   - 500：「サーバーエラーが発生しました」
   - その他：「エラーが発生しました」
3. 標準 `Error` の場合：エラーメッセージをそのまま使用
4. それ以外：「予期しないエラーが発生しました」

## ベストプラクティス

### ✅ 推奨

- バリデーションエラー（422）はフォームにインライン表示
- ネットワークエラーや予期しないエラーは `handleError` でユーザーに通知
- ページコンポーネントは必ず `ErrorBoundary` でラップ
- コンテキスト情報（`component`, `action`）を可能な限り含める

### ❌ 避けるべき

- `console.log` / `console.error` の直接使用（`logger.ts` を使用）
- 空の `catch {}` ブロック（少なくともログは記録する）
- エラーを無視して処理を続行（ユーザーに影響がある場合）

## ログについて

開発環境では、エラーは `logger.ts` を通じてコンソールに出力されます。
本番環境では、将来的に Sentry などの外部サービスへの送信を追加可能です。

```typescript
// 現在の実装
logger.error("Error message", error);

// 将来的な拡張（例）
// if (!isDevelopment && window.Sentry) {
//   window.Sentry.captureException(error);
// }
```

## 関連ファイル

- `resources/js/lib/errorHandler.ts` - エラーハンドリングユーティリティ
- `resources/js/components/ErrorBoundary.tsx` - エラーバウンダリーコンポーネント
- `resources/js/hooks/useErrorHandler.ts` - エラーハンドリングフック
- `resources/js/hooks/useAxiosError.ts` - バリデーションエラー状態管理（Zustand）
- `resources/js/hooks/errorState.ts` - バリデーションエラー状態管理（useState）
- `resources/js/utils/logger.ts` - ロギングユーティリティ
