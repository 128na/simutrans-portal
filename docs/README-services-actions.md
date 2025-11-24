# Services と Actions アーキテクチャドキュメント Index

このディレクトリには、`app/Services/` と `app/Actions/` の役割分担を明確化するための包括的なドキュメントが含まれています。

---

## 📚 ドキュメント一覧

### 1. **完全ガイド** （最初に読むべき）

**[architecture-services-and-actions.md](./architecture-services-and-actions.md)**

Services と Actions の責務分離に関する包括的なガイドラインです。

**内容:**
- アーキテクチャの原則
- Services と Actions の詳細な責務定義
- 実装パターンと Good/Bad Examples
- 命名規則とディレクトリ構造
- テスト戦略
- 既存コードの分析結果

**対象読者:**
- 新規参加者（必読）
- アーキテクチャを理解したい開発者
- 設計の背景を知りたい開発者

**読了時間:** 約30分

---

### 2. **判断フローチャート** （日常的に参照）

**[decision-flowchart-services-actions.md](./decision-flowchart-services-actions.md)**

新しいクラスを作成する際に、どこに配置するかを判断するためのガイドです。

**内容:**
- 配置判断のフローチャート
- 判断基準の詳細な説明
- クイックチェックリスト
- 実例による学習
- よくある質問（FAQ）

**対象読者:**
- 新しいクラスを作成する開発者
- 配置に迷った開発者

**読了時間:** 約15分

---

### 3. **クイックリファレンス** （手元に置く）

**[quick-reference-services-actions.md](./quick-reference-services-actions.md)**

30秒で配置を判断するための簡易リファレンスカードです。

**内容:**
- 判断チャート（30秒版）
- Services vs Actions 一覧表
- 典型例とアンチパターン
- 実装テンプレート
- よくある間違い

**対象読者:**
- 素早く判断したい開発者
- 既にガイドラインを理解している開発者

**読了時間:** 約5分

**推奨:** PDFや画像に変換して手元に置く

---

### 4. **コードレビューチェックリスト** （レビュー時に使用）

**[code-review-checklist-services-actions.md](./code-review-checklist-services-actions.md)**

コードレビュー時に Services と Actions の配置が適切かを確認するためのチェックリストです。

**内容:**
- 配置の妥当性チェック項目
- レッドフラグ（即座に指摘すべき問題）
- 良いコード例
- 命名のチェック
- テストのチェック
- 依存関係のチェック
- レビューコメントのテンプレート

**対象読者:**
- コードレビュアー
- PRを作成する開発者（セルフチェック用）

**読了時間:** 約10分

---

## 🚀 使い方ガイド

### シナリオ1: 初めてプロジェクトに参加した

1. **[完全ガイド](./architecture-services-and-actions.md)** を読む（30分）
2. **[判断フローチャート](./decision-flowchart-services-actions.md)** に目を通す（15分）
3. **[クイックリファレンス](./quick-reference-services-actions.md)** を手元に置く

### シナリオ2: 新しいクラスを作成する

1. **[クイックリファレンス](./quick-reference-services-actions.md)** で判断チャートを確認（30秒）
2. 判断に迷ったら **[判断フローチャート](./decision-flowchart-services-actions.md)** を参照（5分）
3. 実装後、セルフチェックとして **[コードレビューチェックリスト](./code-review-checklist-services-actions.md)** を使用

### シナリオ3: コードレビューする

1. **[コードレビューチェックリスト](./code-review-checklist-services-actions.md)** を開く
2. 各チェック項目を確認しながらレビュー
3. 必要に応じて **[完全ガイド](./architecture-services-and-actions.md)** を参照してコメント

### シナリオ4: 設計の背景を理解したい

1. **[完全ガイド](./architecture-services-and-actions.md)** の「アーキテクチャの原則」セクションを読む
2. 「既存コードの分析」セクションで実例を確認

---

## 📖 学習パス

### 初級（必須）

1. [完全ガイド](./architecture-services-and-actions.md) - Services と Actions の基本理解
2. [クイックリファレンス](./quick-reference-services-actions.md) - 判断チャートを覚える

### 中級（推奨）

3. [判断フローチャート](./decision-flowchart-services-actions.md) - 詳細な判断基準を理解
4. 既存コードを読んで実例を確認

### 上級（任意）

5. [コードレビューチェックリスト](./code-review-checklist-services-actions.md) - レビュー観点を理解
6. アーキテクチャの改善提案

---

## 🎯 要点まとめ

### 覚えておくべき3つのこと

1. **Services = 技術的な関心事** | **Actions = ビジネスの関心事**
2. **Services = 汎用的** | **Actions = 専用的**
3. **Services = HOW（どうやって）** | **Actions = WHAT（何をする）**

### 判断の基本フロー（30秒版）

```
質問1: 外部APIやインフラと通信する？
  → YES: Services/

質問2: 複数のドメインで使える汎用機能？
  → YES: Services/

質問3: 特定のユースケースを表現する？
  → YES: Actions/

それ以外: 既存パターンを再確認
```

---

## 🔗 関連ドキュメント

### プロジェクト全体

- **[README.md](../README.md)** - プロジェクト概要
- **[.github/copilot-instructions.md](../.github/copilot-instructions.md)** - AI エージェント向けガイド

### その他のアーキテクチャドキュメント

- **[openapi-implementation-summary.md](./openapi-implementation-summary.md)** - OpenAPI 実装
- **[openapi-typescript-types.md](./openapi-typescript-types.md)** - TypeScript 型定義

---

## 📝 ドキュメントの更新履歴

| 日付 | バージョン | 変更内容 |
|------|-----------|---------|
| 2025-11-24 | 1.0.0 | 初版リリース: 全4ドキュメントを作成 |

---

## 🤝 貢献

このドキュメントの改善提案や誤りの指摘は歓迎します。

**フィードバック方法:**
1. GitHub Issue を作成
2. Pull Request を送信
3. チームメンバーに直接連絡

---

## 💬 よくある質問

### Q: 既存コードをリファクタリングする必要はありますか？

**A:** いいえ、既存コードは概ね適切に配置されています。新しいコードのみ本ガイドラインに従ってください。

### Q: ガイドラインに従わない特殊なケースはありますか？

**A:** あります。特殊なケースは個別に議論し、判断してください。その結果を Issue や PR で共有すると、ガイドラインの改善に役立ちます。

### Q: 複数のドキュメントのどれを読めばいいですか？

**A:** 以下の基準で選んでください：
- **初めて** → [完全ガイド](./architecture-services-and-actions.md)
- **今すぐ判断したい** → [クイックリファレンス](./quick-reference-services-actions.md)
- **レビュー中** → [コードレビューチェックリスト](./code-review-checklist-services-actions.md)

### Q: AI/Copilot はこのガイドラインを理解していますか？

**A:** はい。[.github/copilot-instructions.md](../.github/copilot-instructions.md) に本ガイドラインの要約が含まれています。

---

**最終更新**: 2025-11-24  
**メンテナー**: Architecture Team
