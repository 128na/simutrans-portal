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

## Directory Structure

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

