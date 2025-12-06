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
│   │   │   ├── SelectCategories.tsx
│   │   │   ├── SelectPostType.tsx
│   │   │   ├── Tags.tsx
│   │   │   ├── TextPre.tsx
│   │   │   ├── TitleH3.tsx
│   │   │   ├── TitleH4.tsx
│   │   │   └── postType/   # 投稿タイプ別表示
│   │   │       ├── AddonIntroduction.tsx
│   │   │       ├── AddonPost.tsx
│   │   │       ├── Markdown.tsx
│   │   │       └── Page.tsx
│   │   ├── forms/          # 記事編集フォーム
│   │   │   ├── CommonForm.tsx
│   │   │   ├── SectionForm.tsx
│   │   │   ├── StatusForm.tsx
│   │   │   └── sections/
│   │   │       ├── SectionCaption.tsx
│   │   │       ├── SectionImage.tsx
│   │   │       ├── SectionText.tsx
│   │   │       └── SectionUrl.tsx
│   │   ├── utils/
│   │   │   └── articleUtil.ts
│   │   ├── ArticleEdit.tsx
│   │   ├── ArticleForm.tsx
│   │   ├── ArticleModal.tsx
│   │   ├── ArticlePreview.tsx
│   │   └── ArticleTable.tsx
│   │
│   ├── attachments/        # 添付ファイル機能
│   │   ├── utils/
│   │   │   └── attachmentUtil.ts
│   │   ├── AttachmentEdit.tsx
│   │   ├── AttachmentManage.tsx
│   │   └── AttachmentTable.tsx
│   │
│   ├── tags/              # タグ機能
│   │   ├── utils/
│   │   │   └── tagUtil.ts
│   │   ├── TagEdit.tsx
│   │   ├── TagModal.tsx
│   │   └── TagTable.tsx
│   │
│   └── user/              # ユーザー/プロフィール機能
│       ├── ProfileEdit.tsx
│       ├── ProfileForm.tsx
│       ├── ProfileLink.tsx
│       └── ProfileShow.tsx
│
├── components/            # 共通UIコンポーネント
│   ├── ui/               # 基本UIコンポーネント
│   │   ├── Accordion.tsx
│   │   ├── Avatar.tsx
│   │   ├── Button.tsx
│   │   ├── ButtonClose.tsx
│   │   ├── ButtonDanger.tsx
│   │   ├── ButtonOutline.tsx
│   │   ├── ButtonSub.tsx
│   │   ├── Checkbox.tsx
│   │   ├── Image.tsx
│   │   ├── Input.tsx
│   │   ├── InputFile.tsx
│   │   ├── Label.tsx
│   │   ├── Link.tsx
│   │   ├── LinkExternal.tsx
│   │   ├── Modal.tsx
│   │   ├── ModalFull.tsx
│   │   ├── Select.tsx
│   │   ├── Textarea.tsx
│   │   ├── TextBadge.tsx
│   │   ├── TextError.tsx
│   │   ├── TextSub.tsx
│   │   ├── Thumbnail.tsx
│   │   └── ui.d.ts
│   ├── layout/           # レイアウトコンポーネント
│   │   ├── DataTable.tsx
│   │   └── Pagination.tsx
│   └── form/             # フォーム関連コンポーネント
│       ├── SelectableSearch.tsx
│       └── Upload.tsx
│
├── hooks/                # 共通hooks（グローバル状態管理）
│   ├── errorState.ts
│   ├── useAnalyticsStore.ts
│   ├── useArticleEditor.ts
│   └── useAxiosError.ts
│
├── utils/                # 共通ユーティリティ
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
