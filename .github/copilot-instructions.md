## Simutrans Portal — Copilot / AI エージェント向けガイド

このリポジトリで AI コーディングエージェントがすぐに作業に入れるように、実用的な注意点を短くまとめています。

## プロジェクト概要

- バックエンド: Laravel (PHP 8.3+, Laravel 12)。主要入口: `artisan` と `composer.json`。
- フロントエンド: React + TypeScript + Vite（旧 README の Quasar/Vue 表記は古い）。フロントエンドのルート: `resources/js/`。
- DB: MySQL（`.env.example` を参照）。テスト: PHPUnit / `php artisan test`。CI カバレッジ: `coverage.128-bit.net`。

## 便利なコマンド

- バックエンド依存をインストール: `composer install`
- フロントエンド依存をインストール: `npm ci` または `npm install`
- フロントエンドのローカル開発: `npm run dev`（Vite 起動）
- フロントエンドアセットのビルド: `npm run build`
- JS の lint/format: `npm run lint`, `npm run format`
- バックエンド一括チェック: `composer run all`（ide-helper, rector, phpstan, pint を順に実行）
- テスト実行: `php artisan test --testsuite=Unit` / `php artisan test --testsuite=Feature`
- コードスタイル整形: `composer run pint`
- 静的解析: `composer run stan`

## 主要な配置とパターン（まずここを確認）

- `routes/` — `web.php`, `api.php`, `internal_api.php` にルーティング定義。コントローラの境界がわかる。
- `app/` — Laravel のアプリコード（`Models`, `Repositories`, `Services`, `Http/Controllers`）。ビジネスロジックは `Repositories` / `Services` に分離されることが多い。
- `resources/js/` — フロントエンドソース。エントリ: `resources/js/front.ts`, `resources/js/mypage.ts`。
- `resources/js/apps/` — React アプリ本体。構成パターン:
    - UI コンポーネント: `components/ui/*`
    - レイアウト系: `components/layout/*`
    - 機能別フォルダ: `features/*`（例: `features/articles`, `features/tags`）
    - グローバル状態: `apps/state/*`（Zustand を使用、例: `useAnalyticsStore.ts`）
    - 型定義: `apps/types/*` — API 変更や props 変更時に更新が必要。
- `public/`, `public/build` — コンパイル済みアセット。手動編集しないこと。

## フロントエンド特有の注意点

- React + TypeScript + Vite 構成。`tsconfig.json` と `vite.config.ts` の設定に注意。
- コンポーネントは小さく保ち、ビジネスロジックは `features/` や `state/` に置く流れ。
- HTTP クライアントは `axios` を利用（`resources/js/apps/*`）。エラー処理は `state/useAxiosError.ts` を参照。
- UI 変更時は `resources/js/apps/types/*.d.ts` の更新を忘れずに。型を更新したら `npm run build` でビルド確認。

## バックエンド特有の注意点

- Laravel の慣習に従う。コントローラは薄く、`Repositories` / `Services` にロジックがあることが多い。
- Composer スクリプト:
    - `composer run all` はリポジトリ全体チェックの定番（IDE ヘルパー生成、rector、phpstan、pint を実行）。
    - `composer run pint`, `composer run stan` で個別にフォーマット／解析可能。
- DB セットアップ: `php artisan migrate --seed`。管理者ユーザー作成は `README.md` の例（`php artisan tinker`）を参照。

## 外部連携 / 依存サービス

- `composer.json` に OneSignal、Discord、Dropbox、ReCAPTCHA、Google API などが含まれる。これら周りの変更は慎重に。
- CI/デプロイで使う環境変数（`.github/workflows` で参照）: `SSH_KEY`, `KNOWN_HOSTS`, `HOST`, `USER`, `APP_DIR`。

## テストとブラウザ自動化

- Laravel Dusk が導入されている（`laravel/dusk`）。ブラウザテストは chromedriver や CI 上の Docker ブラウザが必要になる可能性あり。
- ユニット・機能テストは `php artisan test` または `vendor/bin/phpunit` で実行。

## コードレビューと整形ルール

- PHP: `pint`（コード整形）と `phpstan`（静的解析）を利用。PR 前に `composer run all` を推奨。
- JS/TS: `eslint` と `prettier` を利用。`npm run lint` / `npm run format` を使う。

## 注意事項（特に気をつける点）

- README の Quasar/Vue 記述は古い。現在は React + Vite なので UI 開発は `resources/js/apps` 側を確認。
- ルートと `backend/` に `credential.json` が複数あるため、どの環境向けかを確認すること。
- 多くのフロントエンドファイルは `.tsx`（TypeScript）なので、props を変更する場合は `apps/types` を必ず更新。

## 例 — 迅速な編集ワークフロー

1. ブランチを pull して依存をインストール:

- `composer install`
- `npm ci`

2. 開発サーバを立ち上げる:

- `php artisan serve`（もしくは LAMP / Docker）
- `npm run dev`

3. UI を変更したら型を確認し、ビルドと整形を実行:

- `npm run build`
- `composer run pint`

## 迷ったら最初に見る場所

- フロントエンドの UI バグ: `resources/js/apps/components` と `resources/js/apps/features` を確認。
- API の契約不一致: `routes/api.php` と該当コントローラ (`app/Http/Controllers`) を確認。
- CI や静的解析エラー: ローカルで `composer run stan` と `composer run pint` を実行して再現する。

## PR チェックリスト

- **Lint / Format:** フロントエンドは `npm run lint` と `npm run format`、バックエンドは `composer run pint` を実行して整形しておく。
- **静的解析:** PHP 側は `composer run stan` を実行して問題がないか確認する。
- **依存とビルド:** `composer install` と `npm ci` が通り、フロント変更があれば `npm run build` でビルドが成功すること。
- **テスト:** `php artisan test --testsuite=Unit` と `php artisan test --testsuite=Feature` を通す。Dusk テストは CI 設定が整っている場合のみ実行確認。
- **型の更新:** フロントエンドで props/API を変更したら `resources/js/apps/types/*.d.ts` を必ず更新する。
- **API 契約:** API（`routes/api.php` / コントローラ）を変更した場合、フロントエンドの `axios` 呼び出しと型も合わせて更新し、マイグレーション手順や互換情報を PR 説明に明記する。
- **機密情報:** `credential.json` や `.env` のような秘密情報をコミットしない。必要な設定は環境変数で管理すること。
- **ドキュメント:** README や該当する型定義、API の説明を必要に応じて更新する。
- **PR 説明:** 変更内容、レビュアが落とすべきコマンド（例: `npm run build`, `composer run stan`）、マイグレーションや手動手順があれば記載する。
- **CI の確認:** CI がグリーン（`composer run all` 相当のチェックを含む）になるまでマージしない。

---

## テスト実装方針

- ユニットテスト: `tests/Unit` に配置。主にサービスなどDBに依存しない、もしくはモックに置換可能なロジックのテスト
- 機能テスト: `tests/Feature` に配置。主にController, Repositoryを中心にデータベースに依存するテスト
