# Simutrans Addon Portal

https://simutrans-portal.128-bit.net


## About

Simutransのアドオン投稿サイトのPHPアプリケーションです。


## Setup

一般的なLAMP環境やdockerコンテナなどをご用意ください。

### Required

- PHP:8.0~
- mysql:8.0~

### Optional

- node アセットコンパイルを行う場合に必要

### Install
```
git clone https://github.com/128na/simutrans-portal.git

cd simutrans-portal
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed

npm ci
npm run dev
```

### Test

```
php artisan test
php artisan dusk
```
