# simutrans-portal

## 環境

一般的なLAMP環境やdockerコンテナなどをご用意ください。
PHP:7.1以上
mysql:5.7
redis
node(アセットコンパイルを行う場合に必要)

## setup

1. 依存ファイルのインストール `composer install`
2. `.env.example`をコピーし`.env.`ファイルを作成、環境に合わせて設定する。
3. データベースを作成し、テーブル・初期データを登録する `php artisan migrate --seed`

## setup (アセット)

1. 依存ファイルのインストール `npm install`
2. ビルド `npm run prod`

## テスト

### windows
`./vendor/bin/phpunit.bat`
### other
`phpunit`


## バッチ

### WordPressからのデータインポート（旧サイトからの移行用）

- php artisan import:all
- php artisan import:users
- php artisan import:articles

### リンク切れ自動非公開、ユーザーへ通知

- php artisan check:deadlink

### バックアップ

- php artisan backup:clean
- php artisan backup:run
