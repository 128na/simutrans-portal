# OpenAPI (Swagger) ドキュメント

このディレクトリには、Simutrans Portal API の OpenAPI 仕様とスキーマ定義が含まれています。

## 概要

- **OpenAPI バージョン**: 3.0
- **API バージョン**: 2.0.0
- **パッケージ**: [darkaonline/l5-swagger](https://github.com/DarkaOnLine/L5-Swagger)

## ディレクトリ構造

```
app/OpenApi/
├── OpenApiSpec.php       # メイン OpenAPI 仕様（Info, Servers）
└── Schemas/              # データスキーマ定義
    ├── User.php          # ユーザースキーマ
    ├── Article.php       # 記事スキーマ
    ├── Attachment.php    # 添付ファイルスキーマ
    ├── Tag.php           # タグスキーマ
    ├── Category.php      # カテゴリスキーマ
    ├── ProfileEdit.php   # プロフィール編集スキーマ
    └── Error.php         # エラースキーマ
```

## ドキュメントの生成

OpenAPI ドキュメントを生成するには、以下のコマンドを実行します：

```bash
php artisan l5-swagger:generate
```

生成されたドキュメントは `storage/api-docs/api-docs.json` に保存されます。

## ドキュメントの閲覧

### 開発環境

ローカル開発環境では、以下の URL でドキュメントを閲覧できます：

```
http://localhost:8000/api/documentation
```

### 本番環境

本番環境では、認証が必要です。管理者権限でログイン後、以下の URL にアクセスしてください：

```
https://simutrans-portal.128-bit.net/api/documentation
```

## API エンドポイント

### 認証

すべての API エンドポイントは Laravel Sanctum による認証が必要です。

```
Authorization: Bearer {token}
```

### エンドポイント一覧

#### タグ管理

- `POST /v2/tags` - タグの作成
- `POST /v2/tags/{tag}` - タグの更新

#### 添付ファイル管理

- `POST /v2/attachments` - 添付ファイルのアップロード
- `DELETE /v2/attachments/{attachment}` - 添付ファイルの削除

#### 記事管理

- `POST /v2/articles` - 記事の作成
- `POST /v2/articles/{article}` - 記事の更新

#### プロフィール管理

- `POST /v2/profile` - プロフィールの更新

#### アナリティクス

- `POST /v2/analytics` - アナリティクスデータの取得

## 属性の追加

新しい API エンドポイントを追加する場合は、コントローラーメソッドに OpenAPI 属性を追加してください。

### 例

```php
use OpenApi\Attributes as OA;

#[OA\Post(
  path: '/api/v2/example',
  summary: '例のエンドポイント',
  description: 'エンドポイントの説明',
  tags: ['Example'],
  security: [['sanctum' => []]],
  requestBody: new OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
      properties: [
        new OA\Property(property: 'field', type: 'string', example: 'value'),
      ]
    )
  ),
  responses: [
    new OA\Response(response: 200, description: '成功'),
  ]
)]
public function example(Request $request): JsonResponse
{
    // ...
}
```

## TypeScript 型の生成（オプション）

OpenAPI 仕様から TypeScript の型定義を自動生成できます：

```bash
# openapi-typescript のインストール
npm install -D openapi-typescript

# 型定義の生成
npx openapi-typescript http://localhost:8000/api-docs.json -o resources/js/types/api.d.ts
```

`package.json` にスクリプトを追加：

```json
{
  "scripts": {
    "generate-types": "openapi-typescript http://localhost:8000/api-docs.json -o resources/js/types/api.d.ts"
  }
}
```

## トラブルシューティング

### ドキュメントが生成されない

1. キャッシュをクリアする：

```bash
php artisan config:clear
php artisan cache:clear
```

2. 再度生成を実行：

```bash
php artisan l5-swagger:generate
```

### 属性が認識されない

- 名前空間が正しいか確認してください：`use OpenApi\Attributes as OA;`
- 属性の構文エラーがないか確認してください
- `config/l5-swagger.php` の `paths.annotations` にコントローラーディレクトリが含まれているか確認してください

## 参考資料

- [L5-Swagger GitHub](https://github.com/DarkaOnLine/L5-Swagger)
- [OpenAPI Specification](https://spec.openapis.org/oas/latest.html)
- [Swagger UI](https://swagger.io/tools/swagger-ui/)
- [openapi-typescript](https://github.com/drwpow/openapi-typescript)
