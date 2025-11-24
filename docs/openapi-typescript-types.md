# TypeScript 型定義の自動生成（オプション）

OpenAPI 仕様から TypeScript の型定義を自動生成することで、フロントエンドとバックエンドの型の一貫性を保つことができます。

## 前提条件

- OpenAPI ドキュメントが生成されていること（`php artisan l5-swagger:generate` を実行済み）
- バックエンドサーバーが起動していること

## セットアップ

### 1. openapi-typescript のインストール

```bash
npm install -D openapi-typescript
```

### 2. package.json にスクリプトを追加

```json
{
  "scripts": {
    "generate-api-types": "openapi-typescript http://localhost:8000/api-docs.json -o resources/js/types/api.d.ts"
  }
}
```

### 3. 型定義の生成

```bash
# バックエンドサーバーを起動
php artisan serve

# 別のターミナルで型定義を生成
npm run generate-api-types
```

## 生成される型定義

`resources/js/types/api.d.ts` に以下のような型定義が生成されます：

```typescript
// 例：POSTリクエスト用の型
export interface paths {
  '/v2/tags': {
    post: {
      requestBody: {
        content: {
          'application/json': {
            name: string;
            description: string;
          };
        };
      };
      responses: {
        200: {
          content: {
            'application/json': components['schemas']['Tag'];
          };
        };
      };
    };
  };
}

// 例：スキーマ定義
export interface components {
  schemas: {
    Tag: {
      id: number;
      name: string;
      description: string;
      // ...
    };
  };
}
```

## 型の使用例

### 基本的な使い方

```typescript
import type { components } from '@/types/api';

// スキーマ型の使用
type Tag = components['schemas']['Tag'];
type Article = components['schemas']['Article'];

// 関数での使用
const createTag = async (data: { name: string; description: string }): Promise<Tag> => {
  const response = await axios.post<Tag>('/v2/tags', data);
  return response.data;
};
```

### openapi-fetch の使用（推奨）

より型安全な API クライアントを作成する場合は、`openapi-fetch` の使用を検討してください：

```bash
npm install openapi-fetch
```

```typescript
import createClient from 'openapi-fetch';
import type { paths } from '@/types/api';

const client = createClient<paths>({ baseUrl: 'http://localhost:8000' });

// 型安全な API 呼び出し
const { data, error } = await client.POST('/v2/tags', {
  body: {
    name: 'pak128.japan',
    description: 'pak128.japan用アドオン',
  },
});

if (error) {
  // エラーハンドリング
  console.error(error);
} else {
  // data は自動的に Tag 型として推論される
  console.log(data.id, data.name);
}
```

## CI/CD への統合

GitHub Actions で型定義の生成と検証を自動化できます：

```yaml
# .github/workflows/typescript-types.yml
name: TypeScript Types Check

on:
  push:
    branches: [main, develop]
  pull_request:

jobs:
  check-types:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
      
      - name: Install PHP dependencies
        run: composer install
      
      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: 20
      
      - name: Install Node dependencies
        run: npm ci
      
      - name: Generate OpenAPI docs
        run: php artisan l5-swagger:generate
      
      - name: Start Laravel server
        run: php artisan serve &
        
      - name: Wait for server
        run: sleep 5
      
      - name: Generate TypeScript types
        run: npm run generate-api-types
      
      - name: Check if types are up to date
        run: |
          if [ -n "$(git status --porcelain resources/js/types/api.d.ts)" ]; then
            echo "TypeScript types are not up to date. Please run 'npm run generate-api-types'."
            exit 1
          fi
      
      - name: Type check
        run: npm run typecheck
```

## ベストプラクティス

### 1. 型定義を Git にコミットする

生成された型定義ファイルはリポジトリにコミットすることを推奨します。これにより：

- CI/CD で API の変更を検出できる
- チーム全体で同じ型定義を共有できる
- ビルド時にバックエンドサーバーを起動する必要がない

### 2. API 変更時の手順

1. バックエンドの API を変更
2. OpenAPI アノテーションを更新
3. ドキュメントを生成: `php artisan l5-swagger:generate`
4. 型定義を生成: `npm run generate-api-types`
5. 型エラーがあれば修正
6. 両方のファイルをコミット

### 3. 型定義の整合性チェック

PR 作成時に自動的に型定義が最新かをチェックする仕組みを導入することを推奨します。

## トラブルシューティング

### サーバーに接続できない

```bash
# サーバーが起動しているか確認
curl http://localhost:8000/api-docs.json

# ポートを変更する場合
php artisan serve --port=8001
npm run generate-api-types -- http://localhost:8001/api-docs.json
```

### 型定義が生成されない

1. OpenAPI ドキュメントが正しく生成されているか確認：
   ```bash
   php artisan l5-swagger:generate
   cat storage/api-docs/api-docs.json
   ```

2. URL が正しいか確認：
   ```bash
   curl http://localhost:8000/api-docs.json
   ```

### 型エラーが発生する

- OpenAPI スキーマ定義が正しいか確認
- `nullable: true` や `required` プロパティが適切に設定されているか確認

## 参考資料

- [openapi-typescript](https://github.com/drwpow/openapi-typescript)
- [openapi-fetch](https://github.com/drwpow/openapi-fetch)
- [OpenAPI TypeScript Codegen](https://github.com/ferdikoomen/openapi-typescript-codegen)
