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
```

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

