# Vitest + React Testing Library セットアップ完了

## 実装内容

### インストール済み依存関係

- `vitest`: テストランナー
- `@testing-library/react`: React コンポーネントテスト
- `@testing-library/jest-dom`: DOM マッチャー拡張
- `@testing-library/user-event`: ユーザーインタラクションシミュレーション
- `jsdom`: ブラウザ環境エミュレート
- `@vitest/ui`: テスト結果の UI ビューアー

### 設定ファイル

#### `vite.config.ts`

- テスト環境設定（jsdom）
- グローバル API 有効化
- カバレッジ設定（v8 プロバイダー）
- セットアップファイル指定

#### `tsconfig.json`

- `vitest/globals` 型定義を追加

#### `package.json` スクリプト

- `npm test`: テスト実行（watch モード）
- `npm run test:ui`: UI でテスト結果表示
- `npm run test:coverage`: カバレッジレポート生成

### テストファイル例

#### `resources/js/test/components/Button.test.tsx`

- 基本的な UI コンポーネントのテスト
- props、イベントハンドラー、スタイリングの検証

#### `resources/js/test/features/ArticleTable.test.tsx`

- 複雑な機能コンポーネントのテスト
- フィルタリング、ページネーションの検証

## 使用方法

### テスト実行

```bash
npm test
```

### カバレッジ取得

```bash
npm run test:coverage
```

### UI でテスト確認

```bash
npm run test:ui
```

## テスト作成のガイドライン

### ファイル配置

- `resources/js/test/` 配下に配置
- コンポーネント構造に合わせて `components/`, `features/` などに分類

### 命名規則

- `*.test.tsx` または `*.test.ts`
- テスト対象ファイル名 + `.test.tsx`

### テストパターン

#### コンポーネントテスト

```typescript
import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";

describe("ComponentName", () => {
  it("レンダリングされる", () => {
    render(<ComponentName />);
    expect(screen.getByText("テキスト")).toBeInTheDocument();
  });
});
```

#### ユーザーインタラクション

```typescript
import userEvent from "@testing-library/user-event";

it("ボタンクリック", async () => {
  const user = userEvent.setup();
  render(<Button onClick={mockFn} />);
  await user.click(screen.getByRole("button"));
  expect(mockFn).toHaveBeenCalled();
});
```

#### 非同期処理

```typescript
import { waitFor } from "@testing-library/react";

it("API 呼び出し", async () => {
  render(<Component />);
  await waitFor(() => {
    expect(screen.getByText("ロード完了")).toBeInTheDocument();
  });
});
```

## 今後の拡張

### axios のモック化

```typescript
import { vi } from "vitest";
import axios from "axios";

vi.mock("axios");
const mockedAxios = axios as jest.Mocked<typeof axios>;
```

### Zustand ストアのモック

```typescript
import { useStore } from "@/apps/state/useStore";

vi.mock("@/apps/state/useStore");
```

### カスタムレンダラー（共通プロバイダー）

```typescript
// resources/js/test/utils/test-utils.tsx
export function renderWithProviders(ui: React.ReactElement) {
  return render(
    <SomeProvider>
      {ui}
    </SomeProvider>
  );
}
```

## カバレッジ目標

- コンポーネント: 80%+
- ユーティリティ: 90%+
- クリティカルパス: 100%

## CI 統合

`.github/workflows` にテスト実行を追加予定:

```yaml
- run: npm test
- run: npm run test:coverage
```
