ユーザーへの回答は日本語を使用する。

# 開発ガイドライン

プロジェクトについては /README.md を参照。

コード検索するときはプロジェクトルートの /node_modules, /vendor を含めると検索量が増えるため、 /app など必要なディレクトリのみを行う。

## 1. 作業の進め方

ドキュメント作成の詳細は /docs/README.md を参照。

### 影響範囲が狭い・軽微な修正

そのまま修正作業を行い、`npm run check` と `npm run test` の通過を確認して完了報告する。必要であればテストを追加する。

### 機能追加・リファクタなど横断的な変更

`docs/temp` に一時ドキュメントを作成し、進捗共有可能な状態で進める。影響範囲の調査・タスク洗い出しを行い、依頼者に方針を確認してから実装を開始する。進捗に応じて適宜 `npm run check` と `npm run test` を実行する。

## 2. コーディング規約

詳細なコーディング規約・テスト/セキュリティ/デプロイ/ドキュメント運用ルールは次を参照: [docs/knowledge/coding-standards.md](../docs/knowledge/project-coding-standards-20260101-knowledge.md)

## 3. 参考リソース

- [Laravel公式ドキュメント](https://laravel.com/docs)
- [React公式ドキュメント](https://react.dev)
- [TypeScript公式ドキュメント](https://www.typescriptlang.org/docs)
- [Vite公式ドキュメント](https://vitejs.dev)
- [Tailwind CSS公式ドキュメント](https://tailwindcss.com/docs)
