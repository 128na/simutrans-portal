# Simutrans Addon Portal

https://simutrans-portal.128-bit.net

## About

Simutrans のアドオン投稿サイト「Simutrans Addon Portal」のアプリケーションです。

## Setup

一般的な LAMP 環境や docker コンテナなどをご用意ください。
バックエンドは PHP (Laravel)、フロントエンドは React + TypeScript + Vite で作成しています（フロントエンドのルートは `resources/js/`）。

### Requirements

- PHP:8.3~
- mysql:5.7~
- node:20~
  アセットコンパイルを行う場合に必要

### Backend

```bash
git clone https://github.com/128na/simutrans-portal.git

cd simutrans-portal
cp .env.example .env
composer install
php artisan key:generate
# .envにDB接続情報など必要項目を設定
php artisan migrate --seed
# 管理者作成
php artisan tinker
App\Models\User::create(['role'=>'admin', 'name'=>'your name', 'email'=>'your email', 'password'=>bcrypt('your passowrd')]);
```

メール送信があるため、 `mailpit` などの使用を推奨

### Frontend

フロントエンドは `resources/js/` 以下にあり、React + TypeScript + Vite 構成です。以下は一般的なセットアップと実行手順です（プロジェクトルートで実行します）。

```pwsh
# フロントエンド依存をインストール
npm ci

# ローカル開発サーバを起動（Vite）
npm run dev

# 本番用アセットのビルド
npm run build

# TypeScript型チェック
npm run typecheck

# Lint（自動修正）
npm run lint

# コードフォーマット
npm run format
```

### Type Definitions

フロントエンドの型定義は `resources/js/types/` 配下に体系的に整理されています。

#### ディレクトリ構造

```
resources/js/types/
├── api/                    # API レスポンス型
│   ├── article.d.ts       # 記事API型
│   ├── user.d.ts          # ユーザーAPI型
│   ├── tag.d.ts           # タグAPI型
│   ├── category.d.ts      # カテゴリAPI型
│   ├── attachment.d.ts    # 添付ファイルAPI型
│   ├── analytics.d.ts     # アナリティクスAPI型
│   └── index.ts           # 一括エクスポート
│
├── models/                 # ドメインモデル型（Laravel Modelに対応）
│   ├── Article.ts         # Article モデル型
│   ├── User.ts            # User モデル型
│   ├── Profile.ts         # Profile モデル型
│   ├── Tag.ts             # Tag モデル型
│   ├── Category.ts        # Category モデル型
│   ├── Attachment.ts      # Attachment モデル型
│   ├── FileInfo.ts        # FileInfo モデル型
│   ├── Count.ts           # Count モデル型
│   ├── Common.ts          # 共通型
│   └── index.ts           # 一括エクスポート
│
├── components/             # コンポーネント共通Props型
│   ├── ui.d.ts            # UI コンポーネント型
│   ├── form.d.ts          # フォーム関連型
│   └── index.ts           # 一括エクスポート
│
├── utils/                  # ユーティリティ型
│   ├── pagination.d.ts    # ページネーション型
│   ├── response.d.ts      # 共通レスポンス型
│   └── index.ts           # 一括エクスポート
│
├── analytics.d.ts          # 既存（後方互換性のため残されている）
└── index.d.ts              # 全体のエクスポート + 後方互換性レイヤー
```

#### 使用方法

**新しいコード（推奨）:**

```typescript
// 明示的にインポート
import type { ArticleList, UserShow } from "@/types/models";
import type { ArticleListResponse } from "@/types/api";
import type { PaginatedResponse } from "@/types/utils";

const [articles, setArticles] = useState<ArticleList[]>([]);
const [user, setUser] = useState<UserShow>();
```

**既存コード（後方互換性）:**

```typescript
// グローバル名前空間での使用（既存コードとの互換性のため残されている）
const [articles, setArticles] = useState<Article.List[]>([]);
const [user, setUser] = useState<User.Show>();
```

両方の記法がサポートされていますが、新しいコードでは明示的なインポートを推奨します。

#### 主要な型

**モデル型 (`types/models/`):**

- Laravel のモデルと対応する TypeScript 型
- 公開ページ用（`Show`）とマイページ用（`MypageEdit`, `MypageShow`）を区別
- 例: `ArticleList`, `ArticleShow`, `ArticleMypageEdit`

**API型 (`types/api/`):**

- APIリクエスト/レスポンスの型定義
- 例: `ArticleListResponse`, `ArticleSaveRequest`, `TagCreateRequest`

**ユーティリティ型 (`types/utils/`):**

- `ApiResponse<T>` - 基本的なAPIレスポンスラッパー
- `PaginatedResponse<T>` - Laravel のページネーション構造
- `ValidationError` - バリデーションエラー（422エラー）
- `ErrorResponse` - 標準エラーレスポンス

## Test, Formatter, etc.

### Backend

```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

composer run all
```

## API Documentation

このプロジェクトは OpenAPI (Swagger) を使用して API ドキュメントを自動生成します。

### ドキュメントの生成

```bash
php artisan l5-swagger:generate
```

### ドキュメントの閲覧

- **開発環境**: http://localhost:8000/api/documentation
- **本番環境**: https://simutrans-portal.128-bit.net/api/documentation （要認証）

詳細は [app/OpenApi/README.md](app/OpenApi/README.md) を参照してください。

### API エンドポイント

すべての API エンドポイントは Laravel Sanctum による認証が必要です。

- `POST /v2/tags` - タグの作成
- `POST /v2/tags/{tag}` - タグの更新
- `POST /v2/attachments` - 添付ファイルのアップロード
- `DELETE /v2/attachments/{attachment}` - 添付ファイルの削除
- `POST /v2/articles` - 記事の作成
- `POST /v2/articles/{article}` - 記事の更新
- `POST /v2/profile` - プロフィールの更新
- `POST /v2/analytics` - アナリティクスデータの取得

## Directory Structure

### Controllers

コントローラーは機能と責務に基づいて整理されています：

```
app/Http/Controllers/
├── Auth/                          # 認証関連
│   ├── LoginController.php       # ログイン画面表示
│   ├── TwoFactorController.php   # 2FA画面表示
│   ├── RegisterController.php    # 新規登録・招待
│   └── PasswordController.php    # パスワードリセット
│
├── Pages/                         # 公開ページ
│   ├── TopController.php         # トップページ
│   ├── Article/                  # 記事関連
│   │   ├── IndexController.php   # 一覧（検索・お知らせ・固定ページ）
│   │   ├── ShowController.php    # 詳細表示・フォールバック
│   │   ├── DownloadController.php # ダウンロード・変換
│   │   └── PakController.php     # Pak別一覧
│   ├── UserController.php        # ユーザー一覧・詳細
│   ├── TagController.php         # タグ一覧・詳細
│   ├── CategoryController.php    # カテゴリ一覧・詳細
│   ├── SocialController.php      # SNS連携ページ
│   └── DiscordController.php     # Discord招待
│
├── Mypage/                        # マイページ
│   ├── DashboardController.php   # ダッシュボード・ログイン履歴・2FA設定
│   ├── ProfileController.php     # プロフィール編集
│   ├── AnalyticsController.php   # アナリティクス
│   ├── Article/                  # 記事管理
│   │   ├── IndexController.php   # 記事一覧
│   │   ├── CreateController.php  # 記事作成
│   │   └── EditController.php    # 記事編集
│   ├── AttachmentController.php  # 添付ファイル管理
│   ├── TagController.php         # タグ管理
│   ├── RedirectController.php    # リダイレクト管理
│   └── InviteController.php      # 招待管理
│
├── Admin/                         # 管理画面
│   └── OauthController.php       # OAuth管理
│
├── Controller.php                 # ベースコントローラー
└── RedirectController.php         # 旧URL→新URLリダイレクト・固定リダイレクト
```

各コントローラーは単一責任の原則に従い、明確な責務を持っています。

### アーキテクチャドキュメント

#### Services と Actions

`Services/` と `Actions/` の責務分離については、以下のドキュメントを参照してください：

- **[Services と Actions の役割分担ガイドライン](docs/architecture-services-and-actions.md)** - 詳細なアーキテクチャ説明
- **[配置判断フローチャート](docs/decision-flowchart-services-actions.md)** - 新しいクラスの配置を判断するガイド
- **[Actions README](app/Actions/README.md)** - Actionsの実装パターン
- **[Services README](docs/architecture-services-and-actions.md)** - Servicesの詳細

**要約:**

- **Services** - 外部API連携、インフラ層、汎用ユーティリティ（技術的な関心事）
- **Actions** - ユースケース、ビジネスロジック（ビジネスの関心事）

#### その他の主要コンポーネント

- **[Repositories](app/Repositories/README.md)** - データアクセス層（継承なし設計）
- **[Models](app/Models/README.md)** - Eloquent Model（リレーション、Casts、Scopes）
- **[Enums](app/Enums/README.md)** - 型安全な列挙型（7種類）
- **[Console Commands](app/Console/README.md)** - Artisanコマンド
- **[Jobs](app/Jobs/README.md)** - キュージョブ（非同期処理）
- **[Events & Listeners](app/Events/README.md)** - イベント駆動アーキテクチャ
- **[Routes](routes/README.md)** - ルーティング定義（web, api, internal_api）
- **[Database](database/README.md)** - マイグレーション、Seeder、Factory

### Views (Blade Templates)

Blade テンプレートは、フロントエンド（React + TypeScript）の構造に倣った配置になっています：

```
resources/views/
├── layouts/              # レイアウトテンプレート
│   ├── base.blade.php   # 共通ベースレイアウト
│   ├── front.blade.php  # フロント用（baseを継承）
│   ├── mypage.blade.php # マイページ用（baseを継承）
│   └── admin.blade.php  # 管理画面用（baseを継承）
│
├── components/           # 再利用可能なUIコンポーネント
│   ├── ui/              # 小さなUIパーツ（link, session-message等）
│   ├── layout/          # レイアウト系（header等）
│   └── partials/        # その他の部分テンプレート（ga, meta-tags等）
│
├── pages/               # ページテンプレート
│   ├── top/            # トップページ
│   ├── users/          # ユーザー一覧・詳細
│   ├── tags/           # タグ一覧・詳細
│   ├── categories/     # カテゴリ一覧・詳細
│   ├── pak/            # Pakセット関連
│   ├── search/         # 検索
│   ├── show/           # 記事詳細
│   ├── social/         # ソーシャル関連
│   ├── discord/        # Discord関連
│   ├── announces/      # お知らせ
│   └── static/         # 静的ページ
│
├── auth/                # 認証関連（login, register等）
├── mypage/              # マイページ機能
├── admin/               # 管理画面
├── emails/              # メールテンプレート
└── errors/              # エラーページ
```
