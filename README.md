# Simutrans Addon Portal

https://simutrans-portal.128-bit.net

## About

Simutrans のアドオン投稿サイト「Simutrans Addon Portal」のアプリケーションです。

## Setup

一般的な LAMP 環境や docker コンテナなどをご用意ください。
バックエンドは PHP(Laravel)、フロントエンドは SPA(quasar, vue.js)で作成しています。

フロントエンドの詳細は[こちら](frontend/README.md)

### Requirements

-   PHP:8.3~
-   mysql:5.7~
-   node:20~
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

```bash
cd frontend
npm install
npm run build
```

## Test, Formatter, etc.

### Backend

```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

composer run all
```

### Frontend

```bash
cd frontend
npm run test:e2e

npm run lint
```
