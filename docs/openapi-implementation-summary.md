# OpenAPI (Swagger) ドキュメント実装完了

このドキュメントは、Simutrans Portal への OpenAPI (Swagger) ドキュメント実装の完了報告です。

## 実装内容

### 1. パッケージとコンフィグ

- ✅ `darkaonline/l5-swagger` パッケージを `composer.json` に追加
- ✅ `config/l5-swagger.php` に詳細な設定を記述
- ✅ `.gitignore` に生成ファイルを除外設定

### 2. スキーマ定義

以下のスキーマを `app/OpenApi/Schemas/` に定義：

- ✅ `User.php` - ユーザー情報
- ✅ `Article.php` - 記事情報
- ✅ `Tag.php` - タグ情報
- ✅ `Attachment.php` - 添付ファイル情報
- ✅ `Category.php` - カテゴリ情報
- ✅ `ProfileEdit.php` - プロフィール編集情報
- ✅ `Error.php` - エラーレスポンス

### 3. API エンドポイントのアノテーション

以下のコントローラーに OpenAPI アノテーションを追加：

- ✅ `Mypage\TagController` - タグ作成・更新
- ✅ `Mypage\AttachmentController` - 添付ファイルアップロード・削除
- ✅ `Mypage\Article\CreateController` - 記事作成
- ✅ `Mypage\Article\EditController` - 記事更新
- ✅ `Mypage\ProfileController` - プロフィール更新
- ✅ `Mypage\AnalyticsController` - アナリティクス取得

### 4. ドキュメント

- ✅ `app/OpenApi/README.md` - OpenAPI 使用方法
- ✅ `docs/openapi-typescript-types.md` - TypeScript 型生成ガイド
- ✅ `README.md` - メイン README に API ドキュメント情報を追加

### 5. CI/CD

- ✅ `.github/workflows/openapi-docs.yml` - OpenAPI ドキュメント検証ワークフロー

## 使用方法

### 1. ドキュメントの生成

```bash
php artisan l5-swagger:generate
```

### 2. ドキュメントの閲覧

**開発環境:**
```
http://localhost:8000/api/documentation
```

**本番環境:**
```
https://simutrans-portal.128-bit.net/api/documentation
```

### 3. API エンドポイント一覧

すべてのエンドポイントは Laravel Sanctum 認証が必要です。

#### タグ管理
- `POST /v2/tags` - 新しいタグを作成
- `POST /v2/tags/{tag}` - タグを更新

#### 添付ファイル管理
- `POST /v2/attachments` - ファイルをアップロード
- `DELETE /v2/attachments/{attachment}` - ファイルを削除

#### 記事管理
- `POST /v2/articles` - 新しい記事を作成
- `POST /v2/articles/{article}` - 記事を更新

#### プロフィール管理
- `POST /v2/profile` - プロフィールを更新

#### アナリティクス
- `POST /v2/analytics` - アナリティクスデータを取得

## 今後の拡張

### Phase 4: ドキュメントのテストと検証（未実施）

実際に開発環境でドキュメントを生成してテストすることを推奨：

```bash
# 1. 依存関係をインストール
composer install

# 2. Laravel をセットアップ
cp .env.example .env
php artisan key:generate

# 3. ドキュメントを生成
php artisan l5-swagger:generate

# 4. サーバーを起動
php artisan serve

# 5. ブラウザで確認
# http://localhost:8000/api/documentation
```

### Phase 5: オプション拡張

以下の拡張は必要に応じて実施してください：

#### TypeScript 型の自動生成

```bash
# パッケージをインストール
npm install -D openapi-typescript

# package.json にスクリプトを追加
{
  "scripts": {
    "generate-api-types": "openapi-typescript http://localhost:8000/api-docs.json -o resources/js/types/api.d.ts"
  }
}

# 型定義を生成
npm run generate-api-types
```

詳細は [docs/openapi-typescript-types.md](../docs/openapi-typescript-types.md) を参照。

#### 本番環境での認証設定

本番環境でドキュメントを公開する場合は、認証を設定することを推奨します：

```php
// routes/web.php に追加
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/api/documentation', function () {
        return view('l5-swagger::index');
    });
});
```

#### 公開 API エンドポイントの追加

現在は認証が必要な内部 API のみドキュメント化されています。
今後、公開 API を追加する場合は、同様にアノテーションを追加してください。

## 注意事項

### セキュリティ

- ✅ 本番環境では認証を設定してドキュメントへのアクセスを制限することを推奨
- ✅ `.env` に機密情報を含めないこと
- ✅ GitHub Actions ワークフローに適切な権限設定を実施済み

### メンテナンス

- API を変更した場合は、必ず OpenAPI アノテーションも更新すること
- ドキュメント生成後は動作確認を行うこと
- TypeScript 型を使用する場合は、API 変更時に型定義も更新すること

### パフォーマンス

- 本番環境では `L5_SWAGGER_GENERATE_ALWAYS=false` に設定することを推奨
- ドキュメント生成は CI/CD パイプラインで実行することを推奨

## トラブルシューティング

### ドキュメントが生成されない

```bash
# キャッシュをクリア
php artisan config:clear
php artisan cache:clear

# 再度生成
php artisan l5-swagger:generate
```

### アノテーションが認識されない

- `use OpenApi\Attributes as OA;` が正しくインポートされているか確認
- アノテーションの構文エラーがないか確認
- `config/l5-swagger.php` の `paths.annotations` 設定を確認

## 参考資料

- [L5-Swagger GitHub](https://github.com/DarkaOnLine/L5-Swagger)
- [OpenAPI Specification](https://spec.openapis.org/oas/latest.html)
- [Swagger UI](https://swagger.io/tools/swagger-ui/)
- [openapi-typescript](https://github.com/drwpow/openapi-typescript)

## 実装日

2024年11月24日

## 関連 Issue

[優先度:低] OpenAPI（Swagger）ドキュメントの導入
