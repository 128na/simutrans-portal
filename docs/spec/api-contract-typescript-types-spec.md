# API 契約 TypeScript 型定義仕様

OpenAPI/Swagger 仕様から生成される TypeScript 型定義の管理方針。

---

## 概要

### 背景

Simutrans Portal では API ドキュメント（OpenAPI 3.0）と フロントエンド型定義の同期を取るために、以下のアプローチを採用しています。

- **OpenAPI 仕様が単一の真実の源（SSOT）**
- **TypeScript 型定義は手動で管理**（自動生成より確実）

### 理由

| 手動管理       | 自動生成               |
| -------------- | ---------------------- |
| 型の精度が高い | 依存関係が増える       |
| 実装に適応可能 | ドキュメント同期が困難 |
| IDE 補完が確実 | 更新ロジックが複雑     |

---

## 型定義の構成

### ディレクトリ構造

```
resources/js/types/
├── api/                    # API レスポンス/リクエスト型
│   ├── article.d.ts
│   ├── user.d.ts
│   ├── tag.d.ts
│   └── ...
├── models/                 # ドメインモデル型
│   ├── Article.ts
│   ├── User.ts
│   └── ...
├── components/             # コンポーネント Props 型
│   └── ui.d.ts
├── utils/                  # ユーティリティ型
│   ├── response.d.ts
│   ├── pagination.d.ts
│   └── ...
└── index.d.ts             # 全体エクスポート
```

### API 型 (`api/`)

API エンドポイントのリクエスト/レスポンス型。

```typescript
// types/api/article.d.ts

export namespace ArticleApi {
  // リクエスト
  interface CreateRequest {
    title: string;
    slug: string;
    status: "draft" | "publish" | "private";
    contents: Section[];
  }

  interface UpdateRequest extends Partial<CreateRequest> {}

  // レスポンス
  interface ArticleResponse {
    data: {
      id: number;
      title: string;
      slug: string;
      status: string;
      user_id: number;
      created_at: string;
      updated_at: string;
    };
  }

  interface ListResponse {
    data: ArticleResponse["data"][];
    meta: {
      current_page: number;
      total: number;
      per_page: number;
    };
  }
}
```

### モデル型 (`models/`)

Laravel Model に対応する TypeScript 型。

```typescript
// types/models/Article.ts

export namespace Article {
  interface List {
    id: number;
    title: string;
    slug: string;
    status: "draft" | "publish" | "private";
    post_type: string;
    user_id: number;
    published_at: string | null;
    created_at: string;
  }

  interface Show extends List {
    contents: Section[];
    user: User.Show;
    categories: Category.List[];
    tags: Tag.List[];
  }

  interface MypageEdit extends Show {
    attachments: Attachment.List[];
  }
}
```

### ユーティリティ型 (`utils/`)

共通の型定義。

```typescript
// types/utils/response.d.ts

export interface ApiResponse<T> {
  data: T;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    total: number;
    per_page: number;
    last_page: number;
  };
}

export interface ValidationError {
  [field: string]: string[];
}

export interface ErrorResponse {
  message: string;
  errors?: ValidationError;
}
```

---

## OpenAPI 仕様との同期

### Step 1: OpenAPI アノテーションを記述

```php
// app/Http/Controllers/ArticleController.php

/**
 * @OA\Post(
 *     path="/api/v2/articles",
 *     summary="Create article",
 *     tags={"Articles"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","slug","status"},
 *             @OA\Property(property="title", type="string", example="My Article"),
 *             @OA\Property(property="slug", type="string", example="my-article"),
 *             @OA\Property(property="status", type="string", enum={"draft","publish"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Article created",
 *         @OA\JsonContent(ref="#/components/schemas/Article")
 *     )
 * )
 */
public function store(Request $request): JsonResponse
{
    // ...
}
```

### Step 2: OpenAPI ドキュメントを生成

```bash
php artisan l5-swagger:generate
```

生成されたファイル: `storage/api-docs/api-docs.json`

### Step 3: TypeScript 型を手動で定義

OpenAPI 仕様を参考に、適切な TypeScript 型を作成します。

```typescript
// types/api/article.d.ts - 手動作成

export namespace ArticleApi {
  interface CreateRequest {
    title: string;
    slug: string;
    status: "draft" | "publish";
    contents: Section[];
  }

  interface ArticleResponse {
    data: Article.Show;
  }
}
```

### Step 4: 同期チェックリスト

- [ ] OpenAPI 仕様に新しいエンドポイントを追加した
- [ ] TypeScript 型定義を追加した
- [ ] フロントエンドでインポートテストした（IDE 補完確認）
- [ ] API呼び出しコードで実装テストした

---

## 命名規則

### API リクエスト型

```typescript
// {リソース}Api.{動作}Request

interface ArticleApi {
  StoreRequest; // POST /articles
  UpdateRequest; // POST /articles/{id}
  DeleteRequest; // DELETE /articles/{id}
}
```

### API レスポンス型

```typescript
// {リソース}Api.{リソース}Response

interface ArticleApi {
  ArticleResponse; // 単一リソース
  ListResponse; // リスト（ページネーション含む）
}
```

### モデル型

```typescript
// {モデル}.{用途}

namespace Article {
  List; // 一覧表示用（最小限の属性）
  Show; // 詳細表示用（全属性）
  MypageShow; // マイページ表示用（編集可能属性）
}
```

---

## フロントエンドでの使用

### Import パターン

```typescript
// 明示的にインポート（推奨）
import type { ArticleApi } from "@/types/api/article";
import type { Article } from "@/types/models";

const request: ArticleApi.CreateRequest = {
  title: "New Article",
  slug: "new-article",
  status: "draft",
  contents: [],
};

const response: ArticleApi.ArticleResponse = await api.post(
  "/api/v2/articles",
  request
);
```

### axios 統合

```typescript
// services/articleService.ts

import axios from "axios";
import type { ArticleApi } from "@/types/api/article";
import type { Article } from "@/types/models";

export const createArticle = async (
  data: ArticleApi.CreateRequest
): Promise<Article.Show> => {
  const response = await axios.post<ArticleApi.ArticleResponse>(
    "/api/v2/articles",
    data
  );
  return response.data.data;
};

export const listArticles = async (
  page = 1
): Promise<ArticleApi.ListResponse> => {
  const response = await axios.get<ArticleApi.ListResponse>(
    `/api/v2/articles?page=${page}`
  );
  return response.data;
};
```

---

## 同期メカニズム

### OpenAPI → TypeScript 手動変換ガイド

#### 1. Request Body をマッピング

```yaml
# OpenAPI
requestBody:
  required: true
  content:
    application/json:
      schema:
        type: object
        required:
          - title
          - slug
        properties:
          title:
            type: string
          slug:
            type: string
```

↓ 変換

```typescript
// TypeScript
interface CreateRequest {
  title: string;
  slug: string;
}
```

#### 2. Response Schema をマッピング

```yaml
# OpenAPI
responses:
  "200":
    content:
      application/json:
        schema:
          type: object
          properties:
            data:
              $ref: "#/components/schemas/Article"
```

↓ 変換

```typescript
// TypeScript
interface ArticleResponse {
  data: Article.Show;
}
```

#### 3. Enum を同期

```yaml
# OpenAPI
status:
  type: string
  enum:
    - draft
    - publish
    - private
```

↓ 変換

```typescript
// TypeScript
status: "draft" | "publish" | "private";

// または Union Type
type ArticleStatus = "draft" | "publish" | "private";
```

---

## ベストプラクティス

### ✅ 推奨

- [ ] OpenAPI アノテーションを最初に記述
- [ ] TypeScript 型は OpenAPI から手動派生
- [ ] Namespace を使い関連型をグループ化
- [ ] Union Type よりも Enum 型（型チェック強化）
- [ ] null 許容フィールドは `?` または `| null`

```typescript
// ✅ Good
interface Article {
  id: number;
  title: string;
  published_at: string | null; // null 許容
  status: "draft" | "publish";
}

// Namespace で整理
namespace Article {
  interface List {}
  interface Show extends List {}
}
```

### ❌ 避けるべき

```typescript
// ❌ Bad: any 型
const article: any = response.data;

// ❌ Bad: 型定義なし
const title = response.data.title;

// ❌ Bad: グローバルスコープ
interface Article {}
interface ArticleList extends Article {}

// ❌ Bad: 重複定義
namespace ArticleApi {
  interface CreateRequest {}
  interface CreateResponse {}
}
// vs
namespace Article {
  interface CreateRequest {}
  interface CreateResponse {}
}
```

---

## テスト

### 型チェック

```bash
npm run type  # TypeScript コンパイル (エラー検出)
```

### 型の妥当性テスト

```typescript
// types/__tests__/api.test.ts

import type { ArticleApi } from "@/types/api/article";

describe("ArticleApi types", () => {
  it("CreateRequest has required fields", () => {
    const request: ArticleApi.CreateRequest = {
      title: "Test",
      slug: "test",
      status: "draft",
      contents: [],
    };

    expect(request.title).toBe("Test");
  });
});
```

---

## 関連ドキュメント

- **API ドキュメント**: [OpenAPI README](../../app/OpenApi/README.md)
- **OpenAPI 実装概要**: [OpenAPI Implementation](./openapi-implementation-summary-20260103-log.md)
- **フロントエンド構成**: [resources/js/README.md](../../resources/js/README.md)

---

**最終更新**: 2025-11-24  
**バージョン**: 1.0.0  
**メンテナー**: Development Team
