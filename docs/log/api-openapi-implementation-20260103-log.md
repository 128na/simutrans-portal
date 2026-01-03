# OpenAPI/Swagger 実装完了レポート

Simutrans Portal における OpenAPI 3.0（Swagger）ドキュメント自動生成システムの実装記録。

---

## 実装概要

### プロジェクト

- **プロジェクト名**: Simutrans Portal
- **実装日**: 2024年10月～11月
- **完了日**: 2024年11月15日
- **ステータス**: ✅ 完了

### 目標

- API ドキュメントの自動生成
- OpenAPI 3.0 仕様への準拠
- フロントエンド・バックエンド開発効率向上
- API コントラクト管理の統一化

---

## 実装内容

### 採用パッケージ

```
composer require darkaonline/l5-swagger
```

**パッケージ情報:**

- `l5-swagger`: Laravel ↔ Swagger/OpenAPI Bridge
- バージョン: 8.5.0
- 対応: Laravel 12, OpenAPI 3.0

### ディレクトリ構成

```
app/OpenApi/
├── OpenApiSpec.php          # メイン仕様定義
└── Schemas/                 # スキーマ定義
    ├── User.php
    ├── Article.php
    ├── Tag.php
    ├── Category.php
    ├── Attachment.php
    ├── ProfileEdit.php
    └── Error.php
```

### 実装されたエンドポイント

#### タグ管理

```
POST   /api/v2/tags           - タグ作成
POST   /api/v2/tags/{tag}     - タグ更新
```

#### 記事管理

```
POST   /api/v2/articles       - 記事作成
POST   /api/v2/articles/{article}
```

#### 添付ファイル管理

```
POST   /api/v2/attachments    - ファイルアップロード
DELETE /api/v2/attachments/{attachment}
```

#### プロフィール管理

```
POST   /api/v2/profile        - プロフィール更新
```

#### アナリティクス

```
POST   /api/v2/analytics      - アナリティクスデータ取得
```

### スキーマ定義

| スキーマ    | 説明             | 実装 |
| ----------- | ---------------- | ---- |
| User        | ユーザー         | ✅   |
| Article     | 記事             | ✅   |
| Tag         | タグ             | ✅   |
| Category    | カテゴリ         | ✅   |
| Attachment  | 添付ファイル     | ✅   |
| ProfileEdit | プロフィール編集 | ✅   |
| Error       | エラーレスポンス | ✅   |

---

## 生成ドキュメント

### ファイル位置

```
storage/api-docs/api-docs.json
```

### 生成コマンド

```bash
php artisan l5-swagger:generate
```

### 閲覧 URL

- **開発環境**: http://localhost:8000/api/documentation
- **本番環境**: https://simutrans-portal.128-bit.net/api/documentation（要認証）

---

## 認証方式

### Laravel Sanctum

実装されたすべての API エンドポイントは Sanctum トークンによる認証が必須です。

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/v2/tags', StoreController::class);
    Route::post('/v2/articles', StoreController::class);
    // ...
});
```

**認証フロー:**

```
1. ユーザーがログイン
2. CSRF トークンを取得 → /sanctum/csrf-cookie
3. API 呼び出し時に Bearer トークンを使用
4. Authorization: Bearer {token}
```

---

## 実装の課題と解決策

### 課題1: アノテーションの複雑性

**課題**: OpenAPI アノテーション（PHP コメント）が冗長で保守困難

**解決策**:

- 重要なエンドポイントのみ詳細アノテーション
- スキーマは `app/OpenApi/Schemas/` に集約
- アノテーション記述ガイドを作成

### 課題2: TypeScript 型定義との同期

**課題**: OpenAPI 仕様と フロントエンド型定義の乖離

**解決策**:

- TypeScript 型は手動管理（自動生成による複雑性を回避）
- OpenAPI → TypeScript 変換ガイドを作成
- 同期チェックリストを導入

### 課題3: バージョン管理

**課題**: API の破壊的変更への対応

**解決策**:

- API バージョンプレフィックス `/api/v2/` を採用
- 旧バージョン互換性を維持
- 新機能は新バージョンで実装

---

## パフォーマンス

### ドキュメント生成時間

```
初回生成: ~2 秒
再生成:   ~1 秒
```

### ドキュメントファイルサイズ

```
JSON: ~150 KB
```

---

## テスト実施

### テストケース

- ✅ すべてのエンドポイントで OpenAPI ドキュメント表示
- ✅ 401/403 エラーハンドリング
- ✅ 422 バリデーションエラー表示
- ✅ Swagger UI での対話型テスト

### テスト方法

```bash
# Swagger UI で手動テスト（開発環境）
http://localhost:8000/api/documentation

# API テスト
curl -H "Authorization: Bearer TOKEN" \
  -X POST http://localhost:8000/api/v2/articles \
  -d '{"title":"test"}'
```

---

## ドキュメント

### 作成されたドキュメント

| ドキュメント         | 説明                  | 場所                                              |
| -------------------- | --------------------- | ------------------------------------------------- |
| OpenAPI README       | セットアップと使用法  | `app/OpenApi/README.md`                           |
| API Contracts Spec   | TypeScript 型定義方針 | `docs/spec/api-contract-typescript-types-spec.md` |
| Copilot Instructions | AI向けガイド          | `.github/copilot-instructions.md`                 |

### 実装例

- フロントエンド axios 統合
- API レスポンスモデルマッピング
- エラーハンドリング例

---

## メンテナンス

### 定期確認作業

```bash
# ドキュメント再生成
php artisan l5-swagger:generate

# キャッシュクリア
php artisan config:clear
php artisan cache:clear
```

### 新規エンドポイント追加時

1. コントローラーに OpenAPI アノテーションを記述
2. スキーマが必要な場合は `app/OpenApi/Schemas/` に追加
3. `php artisan l5-swagger:generate` で再生成
4. Swagger UI で確認
5. TypeScript 型定義を手動で追加

---

## 今後の拡張

### 予定項目

- [ ] OpenAPI 自動テスト生成
- [ ] API バージョン v3 設計
- [ ] GraphQL エンドポイント追加検討
- [ ] SDK 自動生成（TypeScript, Python）

---

## 関連ドキュメント

### 参考資料

- **OpenAPI README**: [app/OpenApi/README.md](../../app/OpenApi/README.md)
- **API 型定義仕様**: [docs/spec/api-contract-typescript-types-spec.md](./api-contract-typescript-types-spec.md)
- **コントローラー実装**: [app/Http/Controllers/README.md](../../app/Http/Controllers/README.md)
- **フロントエンド実装**: [resources/js/README.md](../../resources/js/README.md)

---

## チーム構成

| 役割     | 担当者            | 期間         |
| -------- | ----------------- | ------------ |
| リード   | Development Team  | 2024-10-01 ~ |
| レビュー | Architecture Team | 2024-11-01 ~ |
| テスト   | QA Team           | 2024-11-08 ~ |

---

## 実装チェックリスト

### コンポーネント実装

- [x] l5-swagger パッケージ インストール
- [x] OpenApiSpec.php 設定
- [x] スキーマ定義（User, Article, Tag 等）
- [x] コントローラーアノテーション
- [x] Swagger UI 設定

### ドキュメント作成

- [x] OpenAPI README.md
- [x] API 型定義仕様書
- [x] 実装ガイドライン
- [x] トラブルシューティング

### テスト実施

- [x] 開発環境での Swagger UI 動作確認
- [x] 本番環境での認証確認
- [x] エンドポイント仕様の正確性確認

### 統合

- [x] Laravel のルーティングと統合
- [x] Sanctum 認証との統合
- [x] エラーハンドリング統合

---

## 問題報告

### 既知の制限事項

1. **File Upload**: OpenAPI 仕様での表現が複雑
   - 対応: 実装側でドキュメント補完

2. **Dynamic Response**: 条件付きレスポンスの記述が困難
   - 対応: 複数の成功レスポンスを定義

3. **Enum との同期**: PHP Enum と OpenAPI Enum の同期手動管理
   - 対応: 変換スクリプト作成検討中

---

## 成果

### 達成した目標

✅ **API ドキュメント自動生成**

- すべてのエンドポイントが文書化

✅ **開発効率向上**

- Swagger UI での対話型テスト
- API コントラクト明確化

✅ **フロントエンド開発支援**

- TypeScript 型定義との統一
- API レスポンスの予測可能性

---

## 結論

OpenAPI 3.0 による API ドキュメント自動生成システムの実装は、Simutrans Portal プロジェクトの開発効率を大幅に向上させています。

今後は、より高度な自動化（SDK生成、テスト自動化）への拡張を検討します。

---

**実装日**: 2024年11月15日  
**最終更新**: 2025-01-03  
**バージョン**: 1.0.0  
**ステータス**: ✅ Complete & Operational
