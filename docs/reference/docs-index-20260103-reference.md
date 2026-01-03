# ドキュメント索引

Simutrans Portal プロジェクトの全ドキュメントを体系的に整理したマスターインデックス。

---

## 📚 ドキュメント体系

ドキュメントは以下の5つのカテゴリに分類されています:

### 1. 📋 **spec** - 仕様書（ビジネス/技術仕様）

システムの要件、インターフェース、データ構造を定義した公式仕様。

#### PAK パーサー

- [pak-parser-field-standards-spec.md](../spec/pak-parser-field-standards-spec.md)
  - Simutrans PAK ファイルのフィールド命名規則と標準データ構造
- [pak-tunnel-format-spec.md](../spec/pak-tunnel-format-spec.md)
  - PAK トンネルフォーマットの完全バイナリ仕様書

#### API 仕様

- [api-contract-typescript-types-spec.md](../spec/api-contract-typescript-types-spec.md)
  - OpenAPI 仕様から TypeScript 型定義への変換方針

---

### 2. 📖 **knowledge** - 技術解説（実装ガイド・How-To）

実装パターン、ベストプラクティス、詳細な技術解説ドキュメント。

#### Services & Actions アーキテクチャ

- [architecture-services-actions-20260103-knowledge.md](../knowledge/architecture-services-actions-20260103-knowledge.md)
  - Services と Actions の完全ガイド
  - 責務分離、実装パターン、テスト戦略
- [architecture-decision-flowchart-20260103-knowledge.md](../knowledge/architecture-decision-flowchart-20260103-knowledge.md)
  - Services vs Actions 配置判断フローチャート
  - 判断基準、実例、FAQ
- [architecture-quick-reference-20260103-knowledge.md](../knowledge/architecture-quick-reference-20260103-knowledge.md)
  - 30秒で判断するクイックリファレンス
  - 早見表、典型例、アンチパターン
- [architecture-code-review-checklist-20260103-knowledge.md](../knowledge/architecture-code-review-checklist-20260103-knowledge.md)
  - PR レビュー時の確認チェックリスト
  - レッドフラグ、良いコード例、テンプレート

#### エラーハンドリング

- [error-handling-20260103-knowledge.md](../knowledge/error-handling-20260103-knowledge.md)
  - バックエンド・フロントエンド統合エラーハンドリング
  - axios 統合、useErrorHandler フック、ロギング戦略

#### Repository パターン

- [repository-pattern-refactoring-20260103-knowledge.md](../knowledge/repository-pattern-refactoring-20260103-knowledge.md)
  - BaseRepository 廃止とリファクタリング履歴
  - 独立した Repository パターンの実装ガイド

#### PAK パーサー

- [pak-parser-usage-20260103-knowledge.md](../knowledge/pak-parser-usage-20260103-knowledge.md)
  - PAK ファイル抽出の使用ガイド
  - セットアップ、基本的な使い方、実装例
- [pak-parser-implementation-20260103-knowledge.md](../knowledge/pak-parser-implementation-20260103-knowledge.md)
  - PAK パーサーの実装詳細
  - バイナリフォーマット理解、Extractor パターン、デバッグ方法

---

### 3. 📝 **log** - 改修履歴（プロジェクト履歴）

プロジェクトの改修内容、実装完了報告、変更履歴を記録。

#### API 実装

- [api-openapi-implementation-20260103-log.md](../log/api-openapi-implementation-20260103-log.md)
  - OpenAPI/Swagger 実装完了レポート
  - 採用パッケージ、実装内容、課題解決記録

---

### 4. 🔧 **manual** - セットアップ・設定手順

環境構築、デプロイ、運用手順等のマニュアル。

_拡張予定:_

- セットアップガイド
- デプロイ手順
- 運用マニュアル
- トラブルシューティング

---

### 5. 📌 **reference** - 参考・索引

全体構造、インデックス、クイックリファレンス等。

- [docs-index-20260103-reference.md](../reference/docs-index-20260103-reference.md)
  - このドキュメント（マスターインデックス）

---

## 🎯 クイックナビゲーション

### よくある質問から探す

#### アーキテクチャ・設計

**Q: Services と Actions の違いは？**
→ [architecture-services-actions-20260103-knowledge.md](../knowledge/architecture-services-actions-20260103-knowledge.md)

**Q: 新しいクラスをどこに配置すべき？**
→ [architecture-decision-flowchart-20260103-knowledge.md](../knowledge/architecture-decision-flowchart-20260103-knowledge.md)

**Q: PR レビュー時に何をチェックすべき？**
→ [architecture-code-review-checklist-20260103-knowledge.md](../knowledge/architecture-code-review-checklist-20260103-knowledge.md)

#### バックエンド実装

**Q: Repository パターンはどう実装する？**
→ [repository-pattern-refactoring-20260103-knowledge.md](../knowledge/repository-pattern-refactoring-20260103-knowledge.md)

**Q: エラーハンドリングをどうする？**
→ [error-handling-20260103-knowledge.md](../knowledge/error-handling-20260103-knowledge.md)

#### フロントエンド実装

**Q: API レスポンスの TypeScript 型をどう定義する？**
→ [api-contract-typescript-types-spec.md](../spec/api-contract-typescript-types-spec.md)

#### Simutrans 固有機能

**Q: PAK ファイルをどう解析する？**
→ [pak-parser-usage-20260103-knowledge.md](../knowledge/pak-parser-usage-20260103-knowledge.md)

**Q: PAK パーサーの実装詳細は？**
→ [pak-parser-implementation-20260103-knowledge.md](../knowledge/pak-parser-implementation-20260103-knowledge.md)

**Q: PAK フィールド標準仕様は？**
→ [pak-parser-field-standards-spec.md](../spec/pak-parser-field-standards-spec.md)

---

## 📊 ドキュメント分類表

| ドキュメント           | カテゴリ  | 種別           | 対象読者         | 読了時間 |
| ---------------------- | --------- | -------------- | ---------------- | -------- |
| Services & Actions     | knowledge | ガイド         | 全員             | 30分     |
| Decision Flowchart     | knowledge | チェックリスト | 開発者           | 15分     |
| Quick Reference        | knowledge | リファレンス   | 開発者           | 5分      |
| Code Review            | knowledge | チェックリスト | レビュアー       | 10分     |
| Error Handling         | knowledge | ガイド         | フロント・バック | 20分     |
| Repository Pattern     | knowledge | ガイド         | バックエンド     | 15分     |
| PAK Parser Usage       | knowledge | How-To         | バックエンド     | 15分     |
| PAK Parser Impl        | knowledge | 技術詳細       | バックエンド     | 25分     |
| PAK Field Std          | spec      | 仕様書         | バックエンド     | 20分     |
| PAK Tunnel Format      | spec      | 仕様書         | バックエンド     | 40分     |
| API TypeScript         | spec      | 仕様書         | フロント・バック | 20分     |
| OpenAPI Implementation | log       | レポート       | 技術リード       | 10分     |

---

## 🔍 キーワード検索

### アーキテクチャ・デザインパターン

- **Services/Actions**: [Services & Actions](../knowledge/architecture-services-actions-20260103-knowledge.md), [Flowchart](../knowledge/architecture-decision-flowchart-20260103-knowledge.md), [Quick Ref](../knowledge/architecture-quick-reference-20260103-knowledge.md)
- **Repository Pattern**: [Repository Refactoring](../knowledge/repository-pattern-refactoring-20260103-knowledge.md)
- **Error Handling**: [Error Handling Guide](../knowledge/error-handling-20260103-knowledge.md)

### 実装ガイド

- **バックエンド**: [Services & Actions](../knowledge/architecture-services-actions-20260103-knowledge.md), [Repository Pattern](../knowledge/repository-pattern-refactoring-20260103-knowledge.md)
- **フロントエンド**: [API Contracts](../spec/api-contract-typescript-types-spec.md), [Error Handling](../knowledge/error-handling-20260103-knowledge.md)
- **Simutrans**: [PAK Parser Usage](../knowledge/pak-parser-usage-20260103-knowledge.md), [PAK Field Std](../spec/pak-parser-field-standards-spec.md)

### API・統合

- **OpenAPI**: [TypeScript Types](../spec/api-contract-typescript-types-spec.md), [Implementation](../log/api-openapi-implementation-20260103-log.md)
- **型定義**: [TypeScript Contracts](../spec/api-contract-typescript-types-spec.md)

### コードレビュー

- **チェックリスト**: [Code Review](../knowledge/architecture-code-review-checklist-20260103-knowledge.md)
- **判断基準**: [Decision Flowchart](../knowledge/architecture-decision-flowchart-20260103-knowledge.md)

---

## 📅 更新履歴

| 日付       | ドキュメント     | 変更内容                   |
| ---------- | ---------------- | -------------------------- |
| 2025-01-03 | 全体             | ドキュメント整理・分類完了 |
| 2024-11-24 | Services/Actions | 完全ガイド初版             |
| 2024-11-15 | OpenAPI          | 実装完了レポート           |

---

## 📖 学習パス

### 初心者向け（チーム新参者）

1. [README.md](../../README.md) - プロジェクト概要
2. [Services & Actions](../knowledge/architecture-services-actions-20260103-knowledge.md) - アーキテクチャ理解
3. [Decision Flowchart](../knowledge/architecture-decision-flowchart-20260103-knowledge.md) - 判断ガイド

### 中級者向け（既存開発者）

1. [Quick Reference](../knowledge/architecture-quick-reference-20260103-knowledge.md) - 素早い判断
2. [Code Review](../knowledge/architecture-code-review-checklist-20260103-knowledge.md) - レビュー視点
3. [Repository Pattern](../knowledge/repository-pattern-refactoring-20260103-knowledge.md) - 実装パターン

### 上級者向け（アーキテクト・技術リード）

1. [Architecture Details](../knowledge/architecture-services-actions-20260103-knowledge.md) - 詳細設計
2. [Error Handling](../knowledge/error-handling-20260103-knowledge.md) - システム全体設計
3. [API Contracts](../spec/api-contract-typescript-types-spec.md) - 型システム設計

---

## 🤝 貢献・フィードバック

### 改善提案

このドキュメントセットの改善提案は、以下の方法でお願いします：

1. **GitHub Issue**: ドキュメント改善提案用
2. **Pull Request**: ドキュメント修正・追加
3. **チーム会議**: 大規模な変更は事前相談

### 新しいドキュメント追加

新しいドキュメントを追加する場合：

1. **適切なカテゴリを選択** (spec/knowledge/log/manual/reference)
2. **ファイル名**: `{区域}-{トピック}-YYYYMMDD-{種別}.md`
3. **このインデックスを更新**

---

## 🔗 関連リンク

### プロジェクトドキュメント

- **README.md**: [プロジェクト概要](../../README.md)
- **Copilot Instructions**: [AI向けガイド](../../.github/copilot-instructions.md)

### ディレクトリREADME

- **Actions**: [app/Actions/README.md](../../app/Actions/README.md)
- **Repositories**: [app/Repositories/README.md](../../app/Repositories/README.md)
- **Models**: [app/Models/README.md](../../app/Models/README.md)
- **Enums**: [app/Enums/README.md](../../app/Enums/README.md)
- **Console**: [app/Console/README.md](../../app/Console/README.md)
- **Jobs**: [app/Jobs/README.md](../../app/Jobs/README.md)
- **Events**: [app/Events/README.md](../../app/Events/README.md)
- **Routes**: [routes/README.md](../../routes/README.md)
- **Database**: [database/README.md](../../database/README.md)
- **Frontend**: [resources/js/README.md](../../resources/js/README.md)
- **Frontend Tests**: [resources/js/**tests**/README.md](../../resources/js/__tests__/README.md)
- **OpenAPI**: [app/OpenApi/README.md](../../app/OpenApi/README.md)
- **Twitter Service**: [tests/Unit/Services/Twitter/README.md](../../tests/Unit/Services/Twitter/README.md)
- **CI/CD**: [.github/workflows/README.md](../../.github/workflows/README.md)

---

## 📞 サポート

### よくある質問

**Q: どのドキュメントから始めればいい？**
A: [学習パス](#-学習パス) を参考に、あなたのレベルに合わせてお選びください。

**Q: 特定のトピックについて知りたい**
A: [キーワード検索](#-キーワード検索) から関連ドキュメントを探してください。

**Q: ドキュメントに誤りを見つけた**
A: GitHub Issue を作成いただくか、チームに報告してください。

---

**最終更新**: 2025-01-03  
**バージョン**: 1.0.0  
**メンテナー**: Development Team

---

## 付録: ドキュメント一覧（ファイル一覧）

### spec/ フォルダ

```
spec/
├── pak-parser-field-standards-spec.md     # PAK フィールド標準
├── pak-tunnel-format-spec.md              # PAK トンネル仕様
└── api-contract-typescript-types-spec.md  # API TypeScript 型
```

### knowledge/ フォルダ

```
knowledge/
├── architecture-services-actions-20260103-knowledge.md
├── architecture-decision-flowchart-20260103-knowledge.md
├── architecture-quick-reference-20260103-knowledge.md
├── architecture-code-review-checklist-20260103-knowledge.md
├── error-handling-20260103-knowledge.md
├── repository-pattern-refactoring-20260103-knowledge.md
├── pak-parser-usage-20260103-knowledge.md
└── pak-parser-implementation-20260103-knowledge.md
```

### log/ フォルダ

```
log/
└── api-openapi-implementation-20260103-log.md
```

### manual/ フォルダ

```
manual/
(拡張予定)
```

### reference/ フォルダ

```
reference/
└── docs-index-20260103-reference.md  # このドキュメント
```

---
