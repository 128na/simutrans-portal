# テストカバレッジ分析 & 強化ロードマップ

日付: 2026-01-11
最新レポート: 2026-01-11 16:59 JST

## 📊 現在のカバレッジ状況

### 全体サマリー

| メトリクス     | フロント         | バック            | 総合      |
| -------------- | ---------------- | ----------------- | --------- |
| **Statements** | 25.3% (474/1875) | 60.9% (3712/6095) | **52.5%** |
| **Branches**   | 25.6% (307/1199) | N/A               | **25.6%** |
| **Functions**  | 21.4% (154/718)  | 57.6% (537/933)   | **41.9%** |

## 🔴 優先度の高い改善エリア

### フロントエンド（全体: 25.3% Statements）

**課題**: 75%のコードが未テスト状態（非常に低い）

#### 1. **ページコンポーネント（最優先）**

- `front/pages/PublicMyListPage.tsx` - API呼び出しとデータ表示ロジック
- `mypage/pages/` - 複数の管理ページコンポーネント
- `mypage/pages/AnalyticsPage.tsx` - グラフ表示、データ処理

**テスト追加予定**:

- 各ページコンポーネントの初期化テスト
- データ取得時のローディング・エラー状態テスト
- ユーザーインタラクション（フォーム入力など）テスト

#### 2. **フィーチャーコンポーネント（高優先）**

- `features/articles/ArticleEdit.tsx` - 記事編集の複雑なロジック
- `features/articles/ArticleForm.tsx` - フォーム操作と検証
- `features/articles/ArticlePreview.tsx` - プレビュー表示ロジック
- `features/attachments/AttachmentEdit.tsx` - ファイル管理UI
- `features/analytics/AnalyticsGraph.tsx` - グラフレンダリング
- `features/mylist/MyListItemsTable.tsx` - テーブル操作

**テスト追加予定**:

- フォーム入力値の変更テスト
- 削除・編集ボタンのイベント処理
- 条件付きレンダリング（表示/非表示）
- バリデーション結果の表示

#### 3. **UI コンポーネント（中優先）**

- 既存テストで基本的なカバレッジはあるが、エッジケースがない
  - `Button.tsx`, `Input.tsx`, `Modal.tsx` など

**テスト追加予定**:

- disabled 状態のテスト
- 大量テキスト入力のテスト
- スクリーンリーダー対応の確認（accessibility）

#### 4. **Hooks（中優先）**

- `hooks/useArticleEditor.ts` - エディタ状態管理
- `hooks/useAnalyticsStore.ts` - アナリティクス状態

**テスト追加予定**:

- 複数の状態遷移パターン
- エラー発生時の動作

### バックエンド（全体: 60.9% Statements）

**状況**: 中程度のカバレッジだが、まだ改善余地あり

#### 1. **Exception/Error ハンドラー（最優先）**

- `app/Exceptions/` - カスタム例外クラス
- 実際のエラーケースのテストが不足

**テスト追加予定**:

- 各例外の throw される条件
- エラーレスポンスの形式確認

#### 2. **Notification クラス（高優先）**

- `app/Notifications/` - メール送信などのロジック
- 外部サービス連携のテスト

**テスト追加予定**:

- メール送信のモック処理
- テンプレート変数の埋め込み確認

#### 3. **Listener クラス（高優先）**

- `app/Listeners/` - イベントハンドラー
- リレーショナルなビジネスロジック

**テスト追加予定**:

- イベント発火時の副作用の確認
- データベース変更の確認

#### 4. **Cast / Enum（中優先）**

- `app/Casts/` - Eloquent キャスト
- `app/Enums/` - 列挙型

**テスト追加予定**:

- 値の変換・検証ロジック

#### 5. **Trait（低優先）**

- `app/Traits/` - 再利用可能なロジック
- すでに HasCrud は十分なカバレッジあり

---

## 📈 テスト強化ロードマップ

### フェーズ1（即座: 1-2週間）

**目標**: Frontend 35% → 40%, Backend 60% → 65%

```
Frontend:
- ✅ ArticleEdit.tsx（記事編集フロー）
- ✅ ArticleForm.tsx（フォーム操作）
- ✅ AttachmentEdit.tsx（ファイル管理）
- ✅ UI コンポーネント（disabled/エッジケース）

Backend:
- ✅ Exception クラス
- ✅ 重要な Listener
- ✅ Notification のメール生成ロジック
```

### フェーズ2（短期: 2-4週間）

**目標**: Frontend 40% → 50%, Backend 65% → 70%

```
Frontend:
- ✅ ページコンポーネント（各種管理画面）
- ✅ AnalyticsGraph.tsx（グラフ表示）
- ✅ MyListItemsTable.tsx（テーブル操作）

Backend:
- ✅ Cast クラス
- ✅ Enum バリデーション
- ✅ その他 Listener
```

### フェーズ3（中期: 1-2ヶ月）

**目標**: Frontend 50% → 70%, Backend 70% → 80%

```
Frontend:
- ✅ 複数ページのシナリオテスト（Hooks組合）
- ✅ E2E テスト（Playwright）

Backend:
- ✅ 複雑なビジネスロジック（Service層）
- ✅ エッジケースの coverage
```

---

## 🎯 テスト追加時の実装指針

### フロントエンド テストテンプレート

```typescript
// ArticleEdit.test.tsx の例
import { render, screen, fireEvent, waitFor } from "@testing-library/react";
import { ArticleEdit } from "@/features/articles/ArticleEdit";

// 1. 初期状態のテスト
test("renders article form with default values", () => {
  const { getByLabelText } = render(<ArticleEdit articleId={1} />);
  expect(getByLabelText(/title/i)).toHaveValue("existing-title");
});

// 2. ユーザーインタラクションのテスト
test("updates field value on user input", async () => {
  const { getByLabelText } = render(<ArticleEdit articleId={1} />);
  const titleInput = getByLabelText(/title/i);

  fireEvent.change(titleInput, { target: { value: "new-title" } });
  expect(titleInput).toHaveValue("new-title");
});

// 3. API 呼び出しのテスト
test("calls API when saving", async () => {
  const mockAxios = vi.mocked(axios);
  mockAxios.patch.mockResolvedValue({ data: { id: 1 } });

  const { getByRole } = render(<ArticleEdit articleId={1} />);
  fireEvent.click(getByRole("button", { name: /save/i }));

  await waitFor(() => {
    expect(mockAxios.patch).toHaveBeenCalledWith("/api/v1/articles/1", expect.any(Object));
  });
});

// 4. エラー処理のテスト
test("shows error message on API failure", async () => {
  const mockAxios = vi.mocked(axios);
  mockAxios.patch.mockRejectedValue(new Error("API error"));

  const { getByRole, getByText } = render(<ArticleEdit articleId={1} />);
  fireEvent.click(getByRole("button", { name: /save/i }));

  await waitFor(() => {
    expect(getByText(/error|failed/i)).toBeInTheDocument();
  });
});
```

### バックエンド テストテンプレート

```php
// ExceptionTest.php の例
class CustomExceptionTest extends TestCase
{
    // 1. 例外の throw テスト
    public function test_throws_when_article_not_found(): void
    {
        $this->expectException(ArticleNotFoundException::class);
        Article::findOrFail(9999);
    }

    // 2. エラーレスポンスの確認
    public function test_returns_correct_status_code(): void
    {
        $response = $this->actingAs($user)->getJson('/api/v1/articles/9999');
        $response->assertNotFound(); // 404
    }

    // 3. 例外メッセージの確認
    public function test_error_message_is_user_friendly(): void
    {
        $response = $this->actingAs($user)->getJson('/api/v1/articles/9999');
        $response->assertJsonPath('message', 'Article not found');
    }
}
```

---

## 📋 具体的なテスト追加リスト

### 緊急テスト（今週中）

1. **AddToMyList.tsx** - エラー表示ロジック（バグ修正済み）
2. **ArticleEdit.tsx** - 記事保存フロー
3. **Exception クラス** - 各種エラーケース
4. **PublicMyListPage.tsx** - 公開マイリスト表示

### 推奨テスト（今月中）

1. **AnalyticsGraph.tsx** - グラフレンダリング
2. **AttachmentEdit.tsx** - ファイルアップロード
3. **Listener クラス** - イベント処理
4. **Notification クラス** - メール生成

### オプショナル（将来）

1. E2E テスト（Playwright）
2. パフォーマンステスト
3. アクセシビリティテスト

---

## 🚀 実装順序

1. **エラーケースのテスト追加** - 安全性向上
2. **重要なユーザーフロー** - 機能確認
3. **UI コンポーネント** - 使いやすさ
4. **ユーティリティ** - 信頼性

---

## 📌 注意事項

### フロントエンド

- `vi.mock()` を活用して axios や外部依存を隔離
- `act()` で状態更新をラップ（React 警告の回避）
- `waitFor()` で非同期処理を適切に待機

### バックエンド

- `phpunit.xml` の DB 設定を変更しない（MySQL テスト DB のみ）
- 外部 API は Guzzle のモック + `Http::fake()`
- ファイルアップロードは Storage::fake() を使用

---

## 📊 成功指標

| 指標                | 現在  | 目標（1ヶ月） | 目標（3ヶ月） |
| ------------------- | ----- | ------------- | ------------- |
| Frontend Statements | 25.3% | 40%           | 70%           |
| Backend Statements  | 60.9% | 68%           | 80%           |
| 総合カバレッジ      | 52.5% | 56%           | 75%           |
| テスト数            | 679   | 750+          | 900+          |
