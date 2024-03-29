# Simutrans Addon Portal

https://simutrans-portal.128-bit.net

## About

Simutrans のアドオン投稿サイト「Simutrans Addon Portal」のアプリケーションです。

## Setup

一般的な LAMP 環境や docker コンテナなどをご用意ください。
バックエンドは PHP(Laravel)、フロントエンドは SPA(quasar, vue.js)で作成しています。

フロントエンドの詳細は[こちら](frontend/README.md)

### Requirements

-   PHP:8.2~
-   mysql:5.7~
-   node:20~
    アセットコンパイルを行う場合に必要

### Backend

```
git clone https://github.com/128na/simutrans-portal.git

cd simutrans-portal
cp .env.example .env
composer install
php artisan key:generate
// .envにDB接続情報など必要項目を設定
php artisan migrate --seed
```

メール送信があるため、 `mailpit` などの使用を推奨

### Frontend

```bash
npm install
npm run build
```

### Test

```
php artisan dusk:chrome-driver
composer run test

cd frontend
npm run test:e2e
```

### Formatter, Static analysis

```
composer run cs
composer run stan

cd frontend
npm run es
```
