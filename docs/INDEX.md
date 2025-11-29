# ドキュメント索引

プロジェクトの全ドキュメントを体系的に整理しています。目的に応じて適切なドキュメントを参照してください。

## 📖 目次

- [🏠 はじめに](#-はじめに)
- [🏗️ アーキテクチャ](#️-アーキテクチャ)
- [💻 バックエンド（Laravel）](#-バックエンドlaravel)
- [🎨 フロントエンド（React + TypeScript）](#-フロントエンドreact--typescript)
- [🔧 Simutrans 固有機能](#-simutrans-固有機能)
- [🧪 テスト](#-テスト)
- [🚀 CI/CD](#-cicd)
- [🛠️ 開発リソース](#️-開発リソース)

---

## 🏠 はじめに

### プロジェクト全体

- **[README.md](../README.md)** - プロジェクト概要、セットアップ手順、ディレクトリ構造の詳細
- **[CODE_OF_CONDUCT.md](../CODE_OF_CONDUCT.md)** - 行動規範
- **[.github/copilot-instructions.md](../copilot-instructions.md)** - AI エージェント向けクイックガイド

---

## 🏗️ アーキテクチャ

### Services と Actions パターン（重要）

プロジェクトの中核となるアーキテクチャパターン。新しいコードを書く前に必ず確認してください。

- **[docs/README-services-actions.md](README-services-actions.md)** - Services と Actions アーキテクチャの完全ガイド（**最初に読む**）
- **[docs/architecture-services-and-actions.md](architecture-services-and-actions.md)** - 詳細なアーキテクチャ説明
- **[docs/decision-flowchart-services-actions.md](decision-flowchart-services-actions.md)** - 配置判断フローチャート
- **[docs/quick-reference-services-actions.md](quick-reference-services-actions.md)** - クイックリファレンス（30秒で判断）
- **[docs/code-review-checklist-services-actions.md](code-review-checklist-services-actions.md)** - コードレビューチェックリスト

### その他のアーキテクチャドキュメント

- **[docs/repository-pattern-refactoring.md](repository-pattern-refactoring.md)** - Repositoryパターンのリファクタリング履歴
- **[docs/error-handling.md](error-handling.md)** - エラーハンドリング戦略

---

## 💻 バックエンド（Laravel）

### コア機能

- **[app/Actions/README.md](../app/Actions/README.md)** - Actions 実装パターン（ビジネスロジック）
- **[app/Repositories/README.md](../app/Repositories/README.md)** - Repositories（継承なし設計、データアクセス層）
- **[app/Models/README.md](../app/Models/README.md)** - Eloquent Models（リレーション、Casts、Scopes）
- **[app/Enums/README.md](../app/Enums/README.md)** - 型安全な列挙型（7種類）

### 機能別モジュール

- **[app/Console/README.md](../app/Console/README.md)** - Artisanコマンド
- **[app/Jobs/README.md](../app/Jobs/README.md)** - キュージョブ（非同期処理）
- **[app/Events/README.md](../app/Events/README.md)** - イベント駆動アーキテクチャ
- **[app/Http/Controllers/Concerns/README.md](../app/Http/Controllers/Concerns/README.md)** - コントローラートレイト（RespondsWithJson）

### ルーティングとAPI

- **[routes/README.md](../routes/README.md)** - ルーティング定義（web, api, internal_api）
- **[app/OpenApi/README.md](../app/OpenApi/README.md)** - OpenAPI/Swagger ドキュメント
- **[docs/openapi-implementation-summary.md](openapi-implementation-summary.md)** - OpenAPI実装サマリー
- **[docs/openapi-typescript-types.md](openapi-typescript-types.md)** - TypeScript型定義の生成

### データベース

- **[database/README.md](../database/README.md)** - マイグレーション、Seeder、Factory

---

## 🎨 フロントエンド（React + TypeScript）

### 構造とセットアップ

- **[resources/js/README.md](../resources/js/README.md)** - フロントエンドディレクトリ構成の詳細
- **[resources/js/**tests**/README.md](../resources/js/__tests__/README.md)** - フロントエンドテストのセットアップ（Vitest）

---

## 🔧 Simutrans 固有機能

### PAKファイルパーサー

Simutrans の PAK ファイルからメタデータを抽出する機能。

- **[docs/pak-parser-usage.md](pak-parser-usage.md)** - PAKパーサーの使い方
- **[docs/pak-parser-implementation.md](pak-parser-implementation.md)** - PAKパーサーの実装詳細
- **[docs/pak-parser-field-standards.md](pak-parser-field-standards.md)** - フィールド標準仕様
- **[docs/pak-tunnel-format.md](pak-tunnel-format.md)** - トンネルフォーマット仕様

---

## 🧪 テスト

### テスト実装例

- **[tests/Unit/Services/Twitter/README.md](../tests/Unit/Services/Twitter/README.md)** - Twitter PKCE Service のテストドキュメント
- **[tests/Unit/Services/FileInfo/Extractors/file/makeobjs/README.md](../tests/Unit/Services/FileInfo/Extractors/file/makeobjs/README.md)** - makeobj テストデータ
- **[resources/js/**tests**/README.md](../resources/js/__tests__/README.md)** - フロントエンドテスト

---

## 🚀 CI/CD

- **[.github/workflows/README.md](workflows/README.md)** - CI/CD 設定と環境変数

---

## 🛠️ 開発リソース

- **[resources/develop/README.md](../resources/develop/README.md)** - 開発用リソース

---

## 📍 クイックリファレンス

### よくある質問への回答

| 質問                             | ドキュメント                                                                      |
| -------------------------------- | --------------------------------------------------------------------------------- |
| プロジェクト全体を理解したい     | [README.md](../README.md)                                                         |
| 新しいクラスをどこに配置すべき？ | [quick-reference-services-actions.md](quick-reference-services-actions.md)        |
| Services と Actions の違いは？   | [README-services-actions.md](README-services-actions.md)                          |
| Repository の実装方法は？        | [app/Repositories/README.md](../app/Repositories/README.md)                       |
| API の設計方法は？               | [app/OpenApi/README.md](../app/OpenApi/README.md)                                 |
| フロントエンドの構造は？         | [resources/js/README.md](../resources/js/README.md)                               |
| テストの書き方は？               | [tests/Unit/Services/Twitter/README.md](../tests/Unit/Services/Twitter/README.md) |
| PAK ファイルの解析方法は？       | [pak-parser-usage.md](pak-parser-usage.md)                                        |
| エラーハンドリング方針は？       | [error-handling.md](error-handling.md)                                            |
| CI/CD の設定は？                 | [.github/workflows/README.md](workflows/README.md)                                |

---

## 🔄 ドキュメント更新ガイドライン

新しいドキュメントを追加した場合は、以下を更新してください：

1. **このファイル（docs/INDEX.md）** - 適切なカテゴリに追加
2. **[.github/copilot-instructions.md](copilot-instructions.md)** - 必要に応じて参照を追加
3. **関連する README.md** - 関連ドキュメントへのリンクを追加

---

**最終更新**: 2024-11-29  
**メンテナー**: Development Team
