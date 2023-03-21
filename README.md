# Simutrans Addon Portal

https://simutrans-portal.128-bit.net


## About

Simutransのアドオン投稿サイト「Simutrans Addon Portal」のアプリケーションです。

## Setup

一般的なLAMP環境やdockerコンテナなどをご用意ください。
バックエンドはPHP(Laravel)、フロントエンドはSPA(quasar, vue.js)で作成しています。

フロントエンドの詳細は[こちら](frontend/README.md)

### Requirements

- PHP:8.0~
- mysql:5.7~
- node:16~
    アセットコンパイルを行う場合に必要

### Backend

```
git clone https://github.com/128na/simutrans-portal.git

cd simutrans-portal
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
```

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
