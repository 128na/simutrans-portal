# 一括DL仕様
## 機能一覧
### 一括ダウンロード機能
ブックマークに登録したアドオン投稿とアドオン公開のデータを一括ダウンロードできる。

- アドオン記事一覧
- アドオン記事添付ファイル一覧

公開ブックマーク、マイページブックマーク一覧からDL可能

### エクスポート機能
ログインユーザー自身に紐づくデータを一括ダウンロード出来るようにする

- 記事一覧
- 記事添付ファイル一覧

マイページトップからDL可能

## DLフロー
ボタンクリック /api/v3/bulk-zips
DLファイルが生成済み？
Y: 既存の一括DLデータ(bulk_downloads)を返却
N: DLデータを作成、DLファイル作成ジョブ登録して返却

DLデータが完了？
Y: DL開始 /bulk-zips/{uuid}
N: 指定時間後に再チェック5秒とか？

## エントリポイント
### [GET] /api/v3/public-bookmarks/{uuid}/bulk-zip
### [GET] /api/v3/mypage/bookmarks/{id}/bulk-zip
### [GET] /api/v3/mypage/bulk-zip
### [GET] /bulk-zips/{uuid}

## データ構造

bookmarks
bulk_downloads
    id
    uuid
    bulk_zippable_id
    bulk_zippable_type
    generated: 0:生成中,1:完了
    path: nullable,生成完了したら更新

## DLファイル内構造
1つのファイルにまとめる
    記事情報CSV
    attachments/オリジナルファイル名


## commands
```
php artisan make:model BulkZip -m
php artisan make:controller Api/v3/BulkZipController
php artisan make:controller Front/BulkZipController
php artisan make:job BulkZip/CreateBulkZip
php artisan make:resource Api/BulkZipResource
php artisan make:test Controllers/Api/v3/BulkZipControllerTest
php artisan make:test Controllers/Front/BulkZipControllerTest
php artisan make:test Jobs/BulkZip/CreateBulkZipTest

```
