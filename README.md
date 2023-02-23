# Simutrans Addon Portal

https://simutrans-portal.128-bit.net


## About

Simutransのアドオン投稿サイトのPHPアプリケーションです。


## Setup

一般的なLAMP環境やdockerコンテナなどをご用意ください。
フロントエンドは[こちら](frontend/README.md)

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
```

### Test

```
php artisan dusk:chrome-driver
composer run test
```

### Formatter

```
composer run cs
```
