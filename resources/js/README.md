# フロントエンドディレクトリ構成

このディレクトリは認証境界とドメインで整理された React + TypeScript アプリケーションです。

## ディレクトリ構造

```
resources/js/
├── front/                    # フロント（ログイン不要の公開ページ）
│   └── pages/               # ページコンポーネント
│       ├── ArticleListPage.tsx
│       ├── ArticleShowPage.tsx
│       ├── TagSearchPage.tsx
│       └── UserSearchPage.tsx
│
├── mypage/                  # マイページ（ログイン必須の管理ページ）
│   └── pages/               # ページコンポーネント
│       ├── AnalyticsPage.tsx
│       ├── ArticleCreatePage.tsx
│       ├── ArticleEditPage.tsx
│       ├── ArticleListPage.tsx
│       ├── AttachmentPage.tsx
│       ├── ProfileEditPage.tsx
│       └── TagEditPage.tsx
│
├── features/                # 共通機能（ドメイン別）
│   ├── analytics/          # 分析機能
│   │   ├── Analytics.tsx
│   │   ├── AnalyticsGraph.tsx
│   │   ├── AnalyticsOption.tsx
│   │   ├── AnalyticsTable.tsx
│   │   └── utils/
│   │       └── analyticsUtil.ts
│   │
│   ├── articles/           # 記事機能
│   │   ├── components/     # 記事表示コンポーネント
│   │   │   ├── ArticleBase.tsx
│   │   │   ├── ArticleList.tsx
│   │   │   ├── ArticleRelation.tsx
│   │   │   ├── Categories.tsx
│   │   │   ├── PakMetadata.tsx
│   │   │   ├── SelectCategories.tsx
│   │   │   ├── SelectPostType.tsx
│   │   │   ├── Tags.tsx
│   │   │   ├── TextPre.tsx
│   │   │   ├── TitleH3.tsx
│   │   │   ├── TitleH4.tsx
│   │   │   ├── pak/        # Pak関連メタデータ
│   │   │   │   ├── PakGenericMetadata.tsx
│   │   │   │   ├── PakInfoTable.tsx
│   │   │   │   ├── formatter.ts
│   │   │   │   ├── pakBuildingTranslations.ts
│   │   │   │   └── pakConstants.ts
│   │   │   └── postType/   # 投稿タイプ別表示
│   │   │       ├── AddonIntroduction.tsx
│   │   │       ├── AddonPost.tsx
│   │   │       ├── Markdown.tsx
│   │   │       └── Page.tsx
│   │   ├── forms/          # 記事編集フォーム
│   │   │   ├── CommonForm.tsx
│   │   │   ├── SectionForm.tsx
│   │   │   ├── StatusForm.tsx
│   │   │   └── Section/
│   │   │       ├── SectionCaption.tsx
│   │   │       ├── SectionImage.tsx
│   │   │       ├── SectionText.tsx
│   │   │       └── SectionUrl.tsx
│   │   ├── postType/       # 投稿タイプ別フォーム（レガシー）
│   │   │   ├── AddonIntroduction.tsx
│   │   │   ├── AddonPost.tsx
│   │   │   ├── Markdown.tsx
│   │   │   └── Page.tsx
│   │   ├── utils/
│   │   │   └── articleUtil.ts
│   │   ├── ArticleEdit.tsx
│   │   ├── ArticleForm.tsx
│   │   ├── ArticleModal.tsx
│   │   ├── ArticlePreview.tsx
│   │   └── ArticleTable.tsx
│   │
│   ├── attachments/        # 添付ファイル機能
│   │   ├── AttachmentEdit.tsx
│   │   ├── AttachmentManage.tsx
│   │   ├── AttachmentTable.tsx
│   │   ├── attachmentUtil.ts
│   │   └── fileInfoTool.ts
│   │
│   ├── tags/              # タグ機能
│   │   ├── TagEdit.tsx
│   │   ├── TagModal.tsx
│   │   ├── TagTable.tsx
│   │   └── tagUtil.ts
│   │
│   └── user/              # ユーザー/プロフィール機能
│       ├── ProfileEdit.tsx
│       ├── ProfileForm.tsx
│       ├── ProfileIcon.tsx
│       ├── ProfileLink.tsx
│       ├── ProfileShow.tsx
│       └── profileUtil.ts
│
├── components/            # 共通UIコンポーネント
│   ├── ui/               # 基本UIコンポーネント
│   │   ├── Accordion.tsx
│   │   ├── Avatar.tsx
│   │   ├── Button.tsx
│   │   ├── ButtonClose.tsx
│   │   ├── Card.tsx
│   │   ├── Checkbox.tsx
│   │   ├── Checkboxes.tsx
│   │   ├── FormCaption.tsx
│   │   ├── Image.tsx
│   │   ├── Input.tsx
│   │   ├── Link.tsx
│   │   ├── Modal.tsx
│   │   ├── ModalFull.tsx
│   │   ├── MultiColumn.tsx
│   │   ├── Select.tsx
│   │   ├── SortableList.tsx
│   │   ├── Textarea.tsx
│   │   ├── TextBadge.tsx
│   │   ├── TextError.tsx
│   │   ├── TextSub.tsx
│   │   ├── Thumbnail.tsx
│   │   └── ui.d.ts
│   ├── layout/           # レイアウトコンポーネント
│   │   ├── DataTable.tsx
│   │   └── Pagination.tsx
│   ├── form/             # フォーム関連コンポーネント
│   │   ├── SelectableSearch.tsx
│   │   └── Upload.tsx
│   └── ErrorBoundary.tsx # エラーバウンダリー
│
├── hooks/                # 共通hooks（グローバル状態管理）
│   ├── errorState.ts
│   ├── useAnalyticsStore.ts
│   ├── useArticleEditor.ts
│   ├── useAxiosError.ts
│   └── useErrorHandler.ts
│
├── utils/                # 共通ユーティリティ
│   ├── logger.ts
│   └── translate.ts
│
├── types/                # 型定義
│   ├── index.d.ts        # メイン型定義
│   └── analytics.d.ts    # 分析機能の型定義
│
├── lib/                  # レガシー/非Reactスクリプト
│   ├── confirm.ts
│   ├── copy.ts
│   ├── discord.js
│   ├── onesignal.js
│   └── sanctum.ts
│
├── __tests__/           # テストファイル
│   ├── components/
│   ├── features/
│   ├── setup.ts
│   └── README.md
│
├── front.ts             # フロントエントリーポイント
├── mypage.ts            # マイページエントリーポイント
└── vite-env.d.ts        # Vite型定義
```

## 主要コンポーネント一覧

### UI コンポーネント (`components/ui/`)

基本的なUIパーツを提供するコンポーネント群です。

#### フォーム関連

- **Button.tsx** - 基本ボタン（プライマリアクション用）
- **ButtonClose.tsx** - 閉じるボタン（×アイコン）
- **Input.tsx** - テキスト入力フィールド
- **Textarea.tsx** - 複数行テキスト入力
- **Select.tsx** - セレクトボックス
- **Checkbox.tsx** - 単一チェックボックス
- **Checkboxes.tsx** - 複数チェックボックスグループ（複数選択対応）
- **FormCaption.tsx** - フォーム項目のキャプション・説明文

#### レイアウト・表示

- **Card.tsx** - カードコンテナ（枠線付きのコンテンツ表示）
- **Accordion.tsx** - アコーディオン（開閉可能なセクション）
- **Modal.tsx** - モーダルダイアログ
- **ModalFull.tsx** - フルスクリーンモーダル
- **MultiColumn.tsx** - マルチカラムレイアウト（レスポンシブ対応）
- **SortableList.tsx** - ドラッグ&ドロップ可能なソート可能リスト

#### テキスト・バッジ

- **TextBadge.tsx** - バッジ表示（ステータス・タグ等）
- **TextError.tsx** - エラーメッセージ表示
- **TextSub.tsx** - サブテキスト（補足説明等）

#### リンク・ナビゲーション

- **Link.tsx** - 内部リンク

#### 画像

- **Avatar.tsx** - アバター画像（丸型）
- **Image.tsx** - 汎用画像表示
- **Thumbnail.tsx** - サムネイル画像

### レイアウトコンポーネント (`components/layout/`)

- **DataTable.tsx** - データテーブル（ソート・フィルタリング対応）
- **Pagination.tsx** - ページネーション

### フォームコンポーネント (`components/form/`)

- **SelectableSearch.tsx** - 検索可能なセレクトボックス（複数選択対応）
- **Upload.tsx** - ファイルアップロードコンポーネント

### その他

- **ErrorBoundary.tsx** - エラーバウンダリー（エラー時のフォールバック表示）

### 主要な変更点（PR #458）

以下のコンポーネントがPR #458で整理・追加されました：

**新規追加:**

- `Card.tsx` - 統一されたカードスタイルの提供
- `Checkboxes.tsx` - 複数選択チェックボックスの簡易化
- `FormCaption.tsx` - フォーム説明文の統一
- `MultiColumn.tsx` - レスポンシブカラムレイアウト
- `SortableList.tsx` - ドラッグ&ドロップ並び替え機能

**削除されたコンポーネント:**

- `ButtonDanger.tsx`, `ButtonOutline.tsx`, `ButtonSub.tsx` - `Button.tsx` に統合
- `InputFile.tsx` - `Upload.tsx` に統合
- `Label.tsx` - `FormCaption.tsx` に置き換え
- `LinkExternal.tsx` - `Link.tsx` に統合

## 設計原則

### 1. 認証境界による分離

- **front/**: 認証不要の公開ページ
- **mypage/**: 認証必須の管理ページ
- **features/**: 両方で共有されるドメインロジック

### 2. インポートの方向性

```
front/pages → features → components
mypage/pages → features → components
features → components
```

### 3. 機能（feature）の構成

各featureは以下の構造を持ちます：

```
feature/
├── components/      # 表示コンポーネント
├── forms/          # フォームコンポーネント（編集系）
├── hooks/          # feature固有のhooks
└── utils/          # feature固有のユーティリティ
```

## エントリーポイント

### front.ts

```typescript
import "./front/pages/ArticleListPage";
import "./front/pages/ArticleShowPage";
// ...
```

### mypage.ts

```typescript
import "./mypage/pages/ArticleCreatePage";
import "./mypage/pages/ArticleEditPage";
// ...
```

## インポートパスエイリアス

tsconfig.jsonで以下のエイリアスが利用可能です：

- `@/components/*` - 共通UIコンポーネント
- `@/features/*` - 共通機能
- `@/hooks/*` - 共通hooks
- `@/utils/*` - 共通ユーティリティ
- `@/types/*` - 型定義
- `@/lib/*` - レガシースクリプト

## ビルド

```bash
# 開発サーバー起動
npm run dev

# プロダクションビルド
npm run build

# テスト実行
npm run test

# Lint
npm run lint

# フォーマット
npm run format
```

## 追加・変更時のガイドライン

### 新しいページを追加する場合

1. `front/pages/` または `mypage/pages/` に `*Page.tsx` を作成
2. 対応するエントリーポイント（`front.ts` / `mypage.ts`）にインポートを追加
3. **必ず `ErrorBoundary` でラップする**（`components/ErrorBoundary.tsx`）

### 新しい機能を追加する場合

1. `features/` に新しいディレクトリを作成
2. `components/`, `hooks/`, `utils/` サブディレクトリを必要に応じて作成
3. 機能固有の型定義は feature 内に配置

### 共通UIコンポーネントを追加する場合

1. `components/ui/` に配置（基本的なUI部品）
2. `components/layout/` に配置（レイアウト関連）
3. `components/form/` に配置（汎用フォーム部品）

### テストを追加する場合

1. `__tests__/` 配下に対象と同じ構造でテストファイルを配置
2. `*.test.tsx` または `*.test.ts` の命名規則を使用

### エラーハンドリング

エラー処理の詳細については **[docs/error-handling.md](../../docs/error-handling.md)** を参照してください。

基本ルール：

- API エラーは `useErrorHandler` フックまたは `handleError` 関数を使用
- バリデーションエラー（422）は `useAxiosError` でフォームにインライン表示
- `console.log` / `console.error` は直接使用せず `logger.ts` を使用
- ページコンポーネントは `ErrorBoundary` でラップ
