# OpenAPI と TypeScript 型定義（手動管理）

このプロジェクトでは、OpenAPI 仕様とは別に、TypeScript の型定義を手動で管理しています。

## 型定義の構造

型定義は `resources/js/types/` 配下に体系的に整理されています：

```
resources/js/types/
├── api/                    # API レスポンス型
│   ├── article.d.ts       # 記事API型
│   ├── user.d.ts          # ユーザーAPI型
│   ├── tag.d.ts           # タグAPI型
│   ├── category.d.ts      # カテゴリAPI型
│   ├── attachment.d.ts    # 添付ファイルAPI型
│   ├── analytics.d.ts     # アナリティクスAPI型
│   └── index.ts           # 一括エクスポート
│
├── models/                 # ドメインモデル型（Laravel Modelに対応）
│   ├── Article.ts         # Article モデル型
│   ├── User.ts            # User モデル型
│   ├── Profile.ts         # Profile モデル型
│   ├── Tag.ts             # Tag モデル型
│   ├── Category.ts        # Category モデル型
│   ├── Attachment.ts      # Attachment モデル型
│   └── index.ts           # 一括エクスポート
│
├── components/             # コンポーネント共通Props型
│   ├── ui.d.ts            # UI コンポーネント型
│   ├── form.d.ts          # フォーム関連型
│   └── index.ts           # 一括エクスポート
│
├── utils/                  # ユーティリティ型
│   ├── pagination.d.ts    # ページネーション型
│   ├── response.d.ts      # 共通レスポンス型
│   └── index.ts           # 一括エクスポート
│
└── index.d.ts              # 全体のエクスポート + 後方互換性レイヤー
```

## 使用方法

### 基本的な使い方

```typescript
// 明示的にインポート（推奨）
import type { ArticleList, UserShow } from "@/types/models";
import type { ArticleListResponse } from "@/types/api";
import type { PaginatedResponse } from "@/types/utils";

const [articles, setArticles] = useState<ArticleList[]>([]);
const [user, setUser] = useState<UserShow>();
```

### API 呼び出しでの使用例

```typescript
import axios from "axios";
import type { ArticleListResponse, ArticleSaveRequest } from "@/types/api";
import type { ArticleShow } from "@/types/models";

// GET リクエスト
const fetchArticles = async (): Promise<ArticleListResponse> => {
  const response = await axios.get<ArticleListResponse>("/api/v2/articles");
  return response.data;
};

// POST リクエスト
const createArticle = async (
  data: ArticleSaveRequest
): Promise<ArticleShow> => {
  const response = await axios.post<ArticleShow>("/api/v2/articles", data);
  return response.data;
};
```

## OpenAPI ドキュメントとの関係

### OpenAPI の役割

- **API 仕様の文書化**: Swagger UI で API を閲覧
- **API テスト**: Swagger UI から直接 API をテスト
- **契約の明示**: バックエンドとフロントエンドの契約を定義

### TypeScript 型定義の役割

- **型安全性の提供**: コンパイル時の型チェック
- **IDE 補完**: エディタでの自動補完
- **リファクタリング支援**: 型変更時のエラー検出

### なぜ自動生成を使わないのか？

1. **柔軟性**: Laravel の Eloquent リレーションに対応した複雑な型構造
2. **可読性**: 手動管理により意図が明確
3. **保守性**: OpenAPI スキーマと TypeScript 型の完全な一致は困難
4. **既存資産**: 既に整備された型定義体系がある

## 型定義の更新手順

### 1. Laravel モデルを変更した場合

```bash
# 1. マイグレーション実行
php artisan migrate

# 2. IDE Helper を更新（PHPDoc生成）
php artisan ide-helper:models

# 3. TypeScript型定義を手動更新
# resources/js/types/models/ 配下のファイルを編集
```

### 2. API エンドポイントを追加/変更した場合

```bash
# 1. Controller と OpenAPI アノテーションを更新
# 2. OpenAPI ドキュメントを生成
php artisan l5-swagger:generate

# 3. TypeScript型定義を手動更新
# resources/js/types/api/ 配下のファイルを編集

# 4. 型チェック
npm run typecheck
```

## 型定義の例

### API レスポンス型 (types/api/article.d.ts)

```typescript
import type { ArticleList, ArticleShow } from "../models/Article";
import type { PaginatedResponse } from "../utils/response";

// 一覧取得レスポンス
export interface ArticleListResponse extends PaginatedResponse<ArticleList> {}

// 詳細取得レスポンス
export interface ArticleShowResponse {
  data: ArticleShow;
}

// 作成/更新リクエスト
export interface ArticleSaveRequest {
  title: string;
  slug: string;
  status: "draft" | "publish" | "private";
  post_type: "addon-post" | "markdown" | "page";
  contents: ContentSection[];
  category_ids?: number[];
  tag_ids?: number[];
  attachment_ids?: number[];
}
```

### モデル型 (types/models/Article.ts)

```typescript
import type { User } from "./User";
import type { Category } from "./Category";
import type { Tag } from "./Tag";

// 一覧表示用
export interface ArticleList {
  id: number;
  slug: string;
  title: string;
  status: ArticleStatus;
  post_type: ArticlePostType;
  published_at: string | null;
  user: User;
}

// 詳細表示用
export interface ArticleShow extends ArticleList {
  contents: ContentSection[];
  categories: Category[];
  tags: Tag[];
}
```

## ベストプラクティス

### 1. 型定義を Git にコミットする

手動管理の型定義は必ずリポジトリにコミットしてください。

### 2. API 変更時の同期

API を変更したら、必ず対応する TypeScript 型も更新してください：

1. Controller のメソッドを変更
2. OpenAPI アノテーションを更新
3. `php artisan l5-swagger:generate` で仕様を確認
4. TypeScript 型定義を更新
5. `npm run typecheck` で型エラーをチェック

### 3. 型の命名規則

- **モデル型**: `{Model}{用途}` (例: `ArticleList`, `ArticleShow`, `ArticleMypageEdit`)
- **API型**: `{Model}{操作}Response/Request` (例: `ArticleListResponse`, `ArticleSaveRequest`)
- **共通型**: 説明的な名前 (例: `PaginatedResponse`, `ApiResponse`)

## 将来的な選択肢: 自動生成（参考情報）

もし将来的に OpenAPI から TypeScript 型を自動生成したい場合、以下のツールが候補になります：

### openapi-typescript

OpenAPI スキーマから TypeScript 型定義を生成します。

```bash
# インストール
npm install -D openapi-typescript

# 型定義を生成
npx openapi-typescript http://localhost:8000/api-docs.json -o resources/js/types/generated/api.d.ts
```

**メリット**:

- OpenAPI 仕様との完全な同期
- 手動更新の手間が不要

**デメリット**:

- Eloquent リレーションなど Laravel 固有の構造に対応困難
- 生成されたコードは可読性が低い
- カスタマイズが難しい

### openapi-fetch

型安全な fetch ラッパーを生成します。

```bash
npm install openapi-fetch
npm install -D openapi-typescript
```

**使用例**:

```typescript
import createClient from "openapi-fetch";
import type { paths } from "./generated/api";

const client = createClient<paths>({ baseUrl: "/api" });

// 型推論が効く
const { data, error } = await client.GET("/v2/articles", {
  params: { query: { page: 1 } },
});
```

## 自動生成を導入する場合の検討事項

### 1. 既存の手動型定義との共存

```
resources/js/types/
├── generated/           # 自動生成（.gitignore）
│   └── api.d.ts
├── api/                 # 手動管理（カスタム型）
│   └── custom.d.ts
├── models/              # 手動管理（Eloquentモデル対応）
└── index.d.ts           # マージレイヤー
```

### 2. CI/CD での自動更新

```yaml
# .github/workflows/main.yml
- name: Generate TypeScript types
  run: |
    php artisan l5-swagger:generate
    npx openapi-typescript http://localhost:8000/api-docs.json -o resources/js/types/generated/api.d.ts
```

### 3. 手動型定義の優先

自動生成と手動定義を併用する場合、手動定義を優先する設計が推奨されます。

## まとめ

### 現在のアプローチ（手動管理）

✅ **推奨される状況**:

- Laravel 固有の構造（Eloquent リレーション、Casts）が多い
- API 仕様が頻繁に変更される
- チームが TypeScript に習熟している
- 型定義に細かいカスタマイズが必要

### 自動生成アプローチ

✅ **推奨される状況**:

- API 仕様が安定している
- OpenAPI スキーマが正確
- 型の同期が重要
- シンプルな REST API

### 本プロジェクトが手動管理を選択した理由

1. **Laravel との統合**: Eloquent のリレーションや Cast を正確に表現できる
2. **既存資産**: 既に整備された型定義体系がある
3. **柔軟性**: API 仕様と型定義を独立して進化させられる
4. **可読性**: 手動管理により意図が明確で保守しやすい
5. **複雑な構造**: マイページ用 (`MypageEdit`) と公開用 (`Show`) の型を分けるなど、細かい制御が必要

## 関連ドキュメント

- **[OpenAPI README](../app/OpenApi/README.md)** - OpenAPI ドキュメントの生成方法
- **[README.md](../README.md)** - Type Definitions セクション
- **[フロントエンドディレクトリ構成](../resources/js/README.md)** - 型定義の使用例

---

**最終更新**: 2025-01-17  
**メンテナー**: Development Team
