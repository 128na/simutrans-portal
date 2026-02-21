# Coding Standards Skill

## 目的

プロジェクトの規約に従い、保守しやすく一貫したコードを維持する。

## 基本方針

- 詳細規約は `/docs/knowledge/project-coding-standards-20260101-knowledge.md` を参照する
- 既存命名・構成・実装スタイルに合わせる
- 変更は目的に必要な範囲に限定する

## 検索と調査

- コード検索は必要ディレクトリに限定する（例: `/app`, `/resources/js`）
- `/node_modules` と `/vendor` は原則検索対象に含めない

## 検証

- 変更後は `npm run check` と `npm run test` の通過を確認する
- `npm run check` は `format` / `lint --fix` を含み、差分が発生しうることを前提に扱う

## チェックポイント

- 不要なリファクタや整形変更を混ぜていない
- 公開 API や既存契約を壊していない
- 変更理由がコード上で明確である
