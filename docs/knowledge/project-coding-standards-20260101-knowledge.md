# コーディング・運用規約

キーワード: コーディング規約, テスト, デプロイ, セキュリティ, Git, API, DB, ドキュメント
最終更新日：2026-01-03
ステータス：完了

## 概要

バックエンド (Laravel/PHP) とフロントエンド (TypeScript/React) のコーディング規約、DB/API設計方針、テスト・セキュリティ・デプロイ・Git運用・ドキュメント管理のルールをまとめた。日常の開発判断で迷ったときの参照用。なお、作業フローの指示は `.github/copilot-instructions.md` を参照。

## 1. コーディング規約 (PHP / Laravel)

- 単一責任の原則を徹底し、1クラス/1メソッド1責任。
- テストクラスはメソッド単位で実装。FooController::foo は `Feature/Controllers/FooController/FooTest.php`。
- FeatureTest: DBを用いた結合テスト（主にコントローラー/リポジトリ）。
- UnitTest: 外部依存を持たないサービス層を対象（Mock活用）。
- 命名: クラス PascalCase / メソッド camelCase / 変数 camelCase / 定数 UPPER_SNAKE_CASE。
- コードスタイル: Laravel Pint 準拠。コメントは PHPDoc。
- good/bad 例: バリデーション・ビジネスロジック・レスポンスは分離する。

## 2. コーディング規約 (TypeScript / React)

- SPAは禁止。Blade を基盤に必要箇所だけ React を island アーキテクチャでマウント。
- Tailwind クラスを直接多用せず、共通ユーティリティクラスを作成して利用（補助的マージンは可）。
- マージンは原則ボトム側を使用。
- データ受け渡し: `<script type="application/json">` でページ固有データを埋め込み、TSX 側で parse してマウント。
- 命名: コンポーネント PascalCase / 関数・変数 camelCase / 型 PascalCase（頭に I 付けない）/ 定数 UPPER_SNAKE_CASE。
- 設計: 関数コンポーネント + Hooks。Props は型定義必須。
- コードスタイル: ESLint 設定に準拠。

## 3. 共通ルール

- 1ファイル1クラス/コンポーネント。関数は単一責任。
- 1ファイル（1クラス）はおおむね100～200行程度に収め、長くなる場合は責務分割を検討する。
- マジックナンバー禁止、意味のある変数名を使う。
- Lint/format は `npm run check`（tsc/format/lint/pint/phpstan を並列実行）。
- **パス管理**: メソッドの引数や戻り値は相対パスを使用する。フルパスが必要な場合（外部コマンド実行など）はそのメソッド内でフルパスに変換する。

## 4. データベース設計

- テーブル: 複数形 snake_case。カラム: snake_case。FK: `{table}_id`。
- タイムスタンプ: `created_at`, `updated_at`。
- マイグレーション: `php artisan make:migration ...` / `migrate` / `migrate:rollback`。

## 5. API設計

- REST 原則: `/api/v{version}/{resource}`、GET/POST/PUT|PATCH/DELETE。

## 6. テスト戦略

- バックエンド: `php artisan test`（特定フィルタは `--filter`）。
- フロントエンド: `npm run test`（Vitest）。
- E2E: `npm run test:e2e`（必要時）。
- 方針: 単体=主要ロジック、統合=API、E2E=重要フロー。
- 厳守事項: `phpunit.xml` の DB 設定 (`DB_CONNECTION=mysql_test`) は変更禁止。SQLite への切替禁止。変更が必要ならチーム相談。

## 7. セキュリティ

- CSRF 保護（Laravel標準）、XSS 対策（入力エスケープ）、SQL インジェクション対策（Eloquent）、機密情報は環境変数で管理。

## 8. デプロイメント

- 本番チェック: `.env` 確認、`APP_DEBUG=false`、`php artisan config:cache route:cache view:cache`、`npm run build`、DB マイグレーション、ログ監視設定。

## 9. Git運用ルール

- ブランチ: main（本番）、feature/_、bugfix/_、hotfix/\*。
- コミットメッセージ: `<type>: <subject>`（types: feat/fix/docs/style/refactor/test/chore）。

## 10. パフォーマンス最適化

- バックエンド: N+1 回避、キャッシュ活用、適切なインデックス、ページネーション。
- フロントエンド: コード分割、画像最適化、不要再レンダ防止、バンドルサイズ監視。

## 11. ドキュメント管理

- 必須ドキュメント: README, API仕様, DBスキーマ, デプロイ手順, トラブルシュート。
- 運用: docs/README.md に従う。`docs/spec` `docs/manual` `docs/knowledge` `docs/log` は指示がない限り直接編集しない。作業メモは `docs/temp` に置き、完了後整理。

## 12. 開発時の注意事項

- やる: コミット前テスト、コードレビュー、依存更新、適切なログ出力。
- やらない: 機密情報のハードコード、未テストコードのマージ、大規模変更の一括 PR。

## 13. よくある問題と解決策

- マイグレーションエラー: `php artisan migrate:fresh --seed`
- キャッシュ不整合: `php artisan cache:clear config:clear view:clear`
- Node modules 問題: `rm -rf node_modules && npm install`
