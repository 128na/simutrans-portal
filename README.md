# simutrans-portal

https://simutrans.sakura.ne.jp/portal/

## 環境

一般的なLAMP環境やdockerコンテナなどをご用意ください。

### Required

- PHP:7.1~
- mysql:5.7

### Optional

- redis キャッシュ用。あると速い
- node アセットコンパイルを行う場合に必要

## システム構成図

![portal_system](https://user-images.githubusercontent.com/15730241/61630404-98e09a00-acc2-11e9-871f-0d5810640f9b.png)

## setup

1. 依存ファイルのインストール `composer install`
2. `.env.example`をコピーし`.env`ファイルを作成、環境に合わせて設定する。
3. データベースを作成し、テーブル・初期データを登録する `php artisan migrate --seed`

## setup (アセット)

1. 依存ファイルのインストール `npm install`
2. ビルド `npm run prod`

## テスト

### windows
`./vendor/bin/phpunit.bat`
### other
`phpunit`


## バッチ（自動実行）
コマンドスケジューラでそれぞれ毎日実行に指定してあります。
`* * * * * cd /path-to-your-project && php artisan schedule:run`

## バッチ（手動実行）
### リンク切れ自動非公開、ユーザーへ通知

- php artisan check:deadlink

### バックアップ

- php artisan backup:clean
- php artisan backup:run

### アップロード画像一括圧縮

- php artisan compress:image

## API
[APIについて](./docs/api.md)
