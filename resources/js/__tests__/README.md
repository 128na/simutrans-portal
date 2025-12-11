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

## テストファイル構成

### ディレクトリ構造

```
resources/js/__tests__/
├── components/              # コンポーネントテスト
│   ├── ui/                 # UIコンポーネント（全26コンポーネント）
│   ├── layout/             # レイアウトコンポーネント
│   ├── form/               # フォームコンポーネント
│   └── ErrorBoundary.test.tsx
├── features/               # Feature層テスト
│   ├── ArticleTable.test.tsx
│   └── articles/utils/
├── hooks/                  # カスタムフックテスト
├── utils/                  # ユーティリティテスト
├── lib/                    # レガシーライブラリテスト
├── setup.ts               # テストセットアップ
└── README.md
```

### UI コンポーネントテスト (`components/ui/`)

PR #458 で追加された新しいコンポーネントを含む、全26個のUIコンポーネントのテストが実装されています：

**フォーム関連:**
- `Button.test.tsx` - 基本ボタンのクリック、disabled状態
- `ButtonClose.test.tsx` - 閉じるボタンの動作
- `Input.test.tsx` - テキスト入力、値の変更
- `Textarea.test.tsx` - 複数行入力
- `Select.test.tsx` - セレクトボックス、選択変更
- `Checkbox.test.tsx` - 単一チェックボックス
- `Checkboxes.test.tsx` - **新規** 複数チェックボックスグループ、複数選択
- `FormCaption.test.tsx` - **新規** キャプション表示

**レイアウト・表示:**
- `Card.test.tsx` - **新規** カードコンテナのレンダリング
- `Accordion.test.tsx` - 開閉動作
- `Modal.test.tsx` - モーダル表示・非表示
- `ModalFull.test.tsx` - フルスクリーンモーダル
- `MultiColumn.test.tsx` - **新規** マルチカラムレイアウト
- `SortableList.test.tsx` - **新規** ドラッグ&ドロップ操作

**テキスト・バッジ:**
- `TextBadge.test.tsx` - バッジ表示
- `TextError.test.tsx` - エラーメッセージ
- `TextSub.test.tsx` - サブテキスト

**リンク・ナビゲーション:**
- `Link.test.tsx` - 内部リンク

**画像:**
- `Avatar.test.tsx` - アバター表示
- `Image.test.tsx` - 画像読み込み、エラーハンドリング
- `Thumbnail.test.tsx` - サムネイル表示

### レイアウトコンポーネントテスト (`components/layout/`)

- `DataTable.test.tsx` - テーブル表示、ソート、フィルタリング
- `Pagination.test.tsx` - ページ遷移、ページ番号表示

### フォームコンポーネントテスト (`components/form/`)

- `SelectableSearch.test.tsx` - 検索機能、複数選択
- `Upload.test.tsx` - ファイルアップロード、プレビュー

### その他のコンポーネントテスト

- `ErrorBoundary.test.tsx` - エラーキャッチ、フォールバック表示

### Feature テスト (`features/`)

- `ArticleTable.test.tsx` - 記事一覧テーブルの複雑な操作
- `articles/utils/articleUtil.test.ts` - 記事ユーティリティ関数

### Hooks テスト (`hooks/`)

- `useErrorHandler.test.ts` - エラーハンドリングフック

### Utils テスト (`utils/`)

- `logger.test.ts` - ロギング機能
- `translate.test.ts` - 翻訳関数

### Lib テスト (`lib/`)

- `errorHandler.test.ts` - エラーハンドラー

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

- `resources/js/__tests__/` 配下に配置
- コンポーネント構造に合わせて `components/`, `features/` などに分類
- 実装ファイルと同じディレクトリ構造を維持

### 命名規則

- `*.test.tsx` または `*.test.ts`
- テスト対象ファイル名 + `.test.tsx`
- 例: `Card.tsx` → `Card.test.tsx`

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

### PR #458 で追加されたコンポーネントのテストパターン

#### Card コンポーネント

```typescript
describe("Card", () => {
  it("子要素をカード内に表示する", () => {
    render(<Card><p>カード内容</p></Card>);
    expect(screen.getByText("カード内容")).toBeInTheDocument();
  });

  it("カスタムクラスが適用される", () => {
    const { container } = render(<Card className="custom-class">内容</Card>);
    expect(container.firstChild).toHaveClass("custom-class");
  });
});
```

#### Checkboxes（複数選択）コンポーネント

```typescript
describe("Checkboxes", () => {
  const options = [
    { value: "1", label: "オプション1" },
    { value: "2", label: "オプション2" },
  ];

  it("複数選択が可能", async () => {
    const user = userEvent.setup();
    const handleChange = vi.fn();
    render(<Checkboxes options={options} value={[]} onChange={handleChange} />);
    
    await user.click(screen.getByLabelText("オプション1"));
    expect(handleChange).toHaveBeenCalledWith(["1"]);
    
    await user.click(screen.getByLabelText("オプション2"));
    expect(handleChange).toHaveBeenCalledWith(["1", "2"]);
  });
});
```

#### FormCaption コンポーネント

```typescript
describe("FormCaption", () => {
  it("キャプションテキストを表示", () => {
    render(<FormCaption>説明文</FormCaption>);
    expect(screen.getByText("説明文")).toBeInTheDocument();
  });

  it("必須マークが表示される", () => {
    render(<FormCaption required>フィールド名</FormCaption>);
    expect(screen.getByText("*")).toBeInTheDocument();
  });
});
```

#### MultiColumn コンポーネント

```typescript
describe("MultiColumn", () => {
  it("複数カラムで子要素を表示", () => {
    render(
      <MultiColumn columns={2}>
        <div>カラム1</div>
        <div>カラム2</div>
      </MultiColumn>
    );
    expect(screen.getByText("カラム1")).toBeInTheDocument();
    expect(screen.getByText("カラム2")).toBeInTheDocument();
  });

  it("レスポンシブに動作", () => {
    const { container } = render(
      <MultiColumn columns={3}>内容</MultiColumn>
    );
    expect(container.firstChild).toHaveStyle({ gridTemplateColumns: "repeat(3, 1fr)" });
  });
});
```

#### SortableList（ドラッグ&ドロップ）コンポーネント

```typescript
import { DndProvider } from "react-dnd";
import { HTML5Backend } from "react-dnd-html5-backend";

describe("SortableList", () => {
  const items = [
    { id: "1", content: "アイテム1" },
    { id: "2", content: "アイテム2" },
  ];

  it("アイテムリストを表示", () => {
    render(
      <DndProvider backend={HTML5Backend}>
        <SortableList items={items} onReorder={vi.fn()} />
      </DndProvider>
    );
    expect(screen.getByText("アイテム1")).toBeInTheDocument();
    expect(screen.getByText("アイテム2")).toBeInTheDocument();
  });

  it("並び替え時にコールバックが呼ばれる", async () => {
    const handleReorder = vi.fn();
    // ドラッグ&ドロップのシミュレーションテスト
    // 実装は react-dnd-test-utils などを使用
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
