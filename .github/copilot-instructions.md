## Simutrans Portal — Copilot / AI エージェント向けガイド

このリポジトリで AI コーディングエージェントがすぐに作業に入れるように、実用的な注意点を短くまとめています。

---

## 📚 関連ドキュメント

詳細なドキュメントは以下を参照してください：

### 全体構造

- **[README.md](../README.md)** - プロジェクト概要、セットアップ手順、ディレクトリ構造の詳細

### アーキテクチャ

- **[docs/README-services-actions.md](../docs/README-services-actions.md)** - Services と Actions アーキテクチャの完全ガイド
- **[app/Actions/README.md](../app/Actions/README.md)** - Actions 実装パターン
- **[app/Repositories/README.md](../app/Repositories/README.md)** - Repositories（継承なし設計）
- **[app/Models/README.md](../app/Models/README.md)** - Eloquent Models（リレーション、Casts、Scopes）
- **[app/Enums/README.md](../app/Enums/README.md)** - 型安全な列挙型（7種類）

### 機能別

- **[app/Console/README.md](../app/Console/README.md)** - Artisanコマンド
- **[app/Jobs/README.md](../app/Jobs/README.md)** - キュージョブ（非同期処理）
- **[app/Events/README.md](../app/Events/README.md)** - イベント駆動アーキテクチャ
- **[routes/README.md](../routes/README.md)** - ルーティング定義（web, api, internal_api）
- **[database/README.md](../database/README.md)** - マイグレーション、Seeder、Factory

### フロントエンド

- **[resources/js/README.md](../resources/js/README.md)** - フロントエンドディレクトリ構成の詳細
- [resources/js/**tests**/README.md](../resources/js/__tests__/README.md) - フロントエンドテストのセットアップ

### API・その他

- **[app/OpenApi/README.md](../app/OpenApi/README.md)** - OpenAPI/Swagger ドキュメント
- **[tests/Unit/Services/Twitter/README.md](../tests/Unit/Services/Twitter/README.md)** - Twitter PKCE Service のテストドキュメント
- **[.github/workflows/README.md](../.github/workflows/README.md)** - CI/CD 設定と環境変数

---

## プロジェクト概要

- バックエンド: Laravel (PHP 8.3+, Laravel 12)。主要入口: `artisan` と `composer.json`。
- フロントエンド: React + TypeScript + Vite（旧 README の Quasar/Vue 表記は古い）。フロントエンドのルート: `resources/js/`。
- DB: MySQL（`.env.example` を参照）。テスト: PHPUnit / `php artisan test`。

## ディレクトリ（簡易スナップショット）

- `app/` — Laravel のアプリケーションコード（Models, Http/Controllers, Services 等）
- `resources/js/` — フロントエンド (React + TypeScript + Vite)。エントリ: `resources/js/front.ts`, `resources/js/mypage.ts`。
- `routes/` — ルーティング定義 (`web.php`, `api.php`, `internal_api.php`)
- `public/` — 公開フォルダとコンパイル済みアセット（`public/build`）
- `tests/` — PHPUnit テスト（`tests/Unit`, `tests/Feature`）

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

**詳細は [README.md](../README.md) の "Directory Structure" セクションを参照してください。**

### クイックリファレンス

- `routes/` — ルーティング定義 (`web.php`, `api.php`, `internal_api.php`)
- `app/Http/Controllers/` — 機能別に整理（Auth/, Pages/, Mypage/, Admin/）
- `app/Services/` と `app/Actions/` — 技術的関心事とビジネスロジックの分離（詳細: [docs/README-services-actions.md](../docs/README-services-actions.md)）
- `app/Repositories/` — データアクセス層（継承を使わず独立したクラス）
- `resources/js/` — フロントエンド React + TypeScript（詳細: [resources/js/README.md](../resources/js/README.md)）
- `resources/views/` — Blade テンプレート
- `public/build/` — コンパイル済みアセット（手動編集禁止）

## フロントエンド特有の注意点

**詳細は [resources/js/README.md](../resources/js/README.md) を参照してください。**

### 重要ポイント

### 重要ポイント

- React + TypeScript + Vite 構成
- エントリポイント: `front.ts`, `mypage.ts`
- HTTP クライアント: `axios`（エラー処理は `state/useAxiosError.ts`）
- ロギング: `resources/js/utils/logger.ts` を使用（console.log 直接使用禁止）
- テスト: Vitest + React Testing Library（詳細: [resources/js/**tests**/README.md](../resources/js/__tests__/README.md)）

### 型定義の配置ルール

**詳細は [README.md](../README.md) の "Type Definitions" セクションを参照してください。**

型定義は `resources/js/types/` 配下に体系的に整理：

- **`types/models/`** - Laravel モデルに対応する TypeScript 型
- **`types/api/`** - API リクエスト/レスポンスの型
- **`types/utils/`** - ユーティリティ型（response, pagination）
- **`types/components/`** - コンポーネント共通Props型

**推奨:** 新しいコードでは明示的なインポートを使用

```typescript
import type { ArticleList, UserShow } from "@/types/models";
import type { ArticleListResponse } from "@/types/api";
```

## バックエンド特有の注意点

### Composer スクリプト

- `composer run all` — 全チェック（ide-helper, rector, phpstan, pint）
- `composer run pint` — コード整形
- `composer run stan` — 静的解析

### Services と Actions の責務分離

**重要**: 新しいコードを作成する際は、責務を明確に区別してください。

**詳細は [docs/README-services-actions.md](../docs/README-services-actions.md) を参照してください。**

#### クイックガイド

- **Services (app/Services/)**: 技術的な関心事
  - 外部API通信（Twitter, Discord, BlueSky, Misskey, Google等）
  - インフラ層のラッパー（ファイルシステム、キャッシュ、メール等）
  - 汎用的なユーティリティ（Markdown変換、Feed生成等）
  - 命名: `{機能名}Service` または `{サービス名}ApiClient`

- **Actions (app/Actions/)**: ビジネスの関心事
  - 1つの具体的なユースケース（記事作成、ユーザー登録等）
  - アプリケーション固有のビジネスルール
  - 単一責任の原則（1クラス = 1ユースケース）
  - 命名: 動詞で始める（`StoreArticle`, `UpdateArticle`）

**判断フロー**:

1. 外部APIやインフラと通信する？ → `Services/`
2. 複数のドメインで再利用される？ → `Services/`
3. 特定のユースケースを表現する？ → `Actions/`

### Repository パターン

**重要**: Repository に継承を使用しません。各 Repository は独立したクラスです。

- `BaseRepository` は**非推奨**（継承禁止）
- 必要なメソッドのみを実装
- モデルは `private readonly` プロパティとして受け取る

```php
final class ArticleRepository
{
    public function __construct(public Article $model) {}

    public function find(int $id): ?Article
    {
        return $this->model->find($id);
    }
}
```

## テストとCI/CD

### テスト実行

- バックエンド: `php artisan test --testsuite=Unit` / `--testsuite=Feature`
- フロントエンド: `npm test` / `npm run test:coverage`
- 参考: [resources/js/**tests**/README.md](../resources/js/__tests__/README.md)、[tests/Unit/Services/Twitter/README.md](../tests/Unit/Services/Twitter/README.md)

### CI/CD

- 環境変数: `SSH_KEY`, `KNOWN_HOSTS`, `HOST`, `USER`, `APP_DIR`
- 詳細: [.github/workflows/README.md](../.github/workflows/README.md)

## API ドキュメント

- パッケージ: darkaonline/l5-swagger（OpenAPI 3.0）
- 生成: `php artisan l5-swagger:generate`
- 閲覧: http://localhost:8000/api/documentation（開発環境）
- 詳細: [app/OpenApi/README.md](../app/OpenApi/README.md)

## 注意事項（特に気をつける点）

- ルートに `credential.json` があるため、どの環境向けかを確認すること。
- 多くのフロントエンドファイルは `.tsx`（TypeScript）なので、props を変更する場合は `types/` を必ず更新。

## 例 — 迅速な編集ワークフロー

1. ブランチを pull して依存をインストール:

   ```bash
   composer install
   npm ci
   ```

2. 開発サーバを立ち上げる:

   ```bash
   php artisan serve
   npm run dev
   ```

3. UI を変更したら型を確認し、ビルドと整形を実行:
   ```bash
   npm run build
   composer run pint
   ```

## 迷ったら最初に見る場所

- **フロントエンドの UI バグ**: `resources/js/components` と `resources/js/features` を確認 → [resources/js/README.md](../resources/js/README.md)
- **コントローラーの配置**: `Auth/`, `Pages/`, `Mypage/`, `Admin/` で機能別分類 → [README.md](../README.md)
- **Services/Actions の判断**: [docs/README-services-actions.md](../docs/README-services-actions.md) の判断フローチャート
- **API の契約不一致**: `routes/api.php` と該当コントローラを確認 → [app/OpenApi/README.md](../app/OpenApi/README.md)
- **型定義の場所**: `resources/js/types/` 配下 → [README.md](../README.md) の Type Definitions
- **テストの書き方**: [resources/js/**tests**/README.md](../resources/js/__tests__/README.md)、[tests/Unit/Services/Twitter/README.md](../tests/Unit/Services/Twitter/README.md)
- **CI エラー**: [.github/workflows/README.md](../.github/workflows/README.md)

## PR チェックリスト

- **Lint / Format:** フロントエンドは `npm run lint` と `npm run format`、バックエンドは `composer run pint` を実行して整形しておく。
- **静的解析:** PHP 側は `composer run stan` を実行して問題がないか確認する。
- **依存とビルド:** `composer install` と `npm ci` が通り、フロント変更があれば `npm run build` でビルドが成功すること。
- **テスト:** `php artisan test --testsuite=Unit` と `php artisan test --testsuite=Feature` を通す。Dusk テストは CI 設定が整っている場合のみ実行確認。
- **型の更新:** フロントエンドで props/API を変更したら `resources/js/types/` 配下の型定義を必ず更新する。
  - モデル変更: `types/models/*.ts`
  - API 変更: `types/api/*.d.ts`
  - コンポーネント Props: `types/components/*.d.ts`
- **API 契約:** API（`routes/api.php` / コントローラ）を変更した場合、フロントエンドの `axios` 呼び出しと型も合わせて更新し、マイグレーション手順や互換情報を PR 説明に明記する。
- **機密情報:** `credential.json` や `.env` のような秘密情報をコミットしない。必要な設定は環境変数で管理すること。
- **ドキュメントの同期:** コード変更時は関連するREADMEやドキュメントも確認し、差異があれば更新する。
  - 新しい Action/Service/Repository を追加 → 該当ディレクトリの README を更新
  - Enum の値を追加/変更 → `app/Enums/README.md` を更新
  - 新しいコマンドを追加 → `app/Console/README.md` を更新
  - 新しいジョブを追加 → `app/Jobs/README.md` を更新
  - 新しいイベント/リスナーを追加 → `app/Events/README.md` を更新
  - マイグレーションを追加 → `database/README.md` の「最近の主要マイグレーション」を更新
  - ルートを追加/変更 → `routes/README.md` を更新
  - フロントエンドの構造を変更 → `resources/js/README.md` を更新
  - テスト方針を変更 → 該当する README（`resources/js/__tests__/README.md` 等）を更新
- **PR 説明:** 変更内容、レビュアが落とすべきコマンド（例: `npm run build`, `composer run stan`）、マイグレーションや手動手順があれば記載する。
- **CI の確認:** CI がグリーン（`composer run all` 相当のチェックを含む）になるまでマージしない。

## 未実装のテスト候補

以下はリポジトリ走査により「テストで直接参照されていない」可能性が高いクラス／機能の候補です。自動検出のため誤検知があり得ます。優先度は概ね重要度と外部依存の有無で分けています。

### Services（外部API・インフラ層）

- **`App\Services\Twitter\TwitterV2Api`**: Twitter API クライアント。推奨: 成功・HTTPエラー・例外処理のモック化テスト。
- **`App\Services\Misskey\MisskeyApiClient`**: Misskey クライアント。推奨: API レスポンスの正規化テスト。
- **`App\Services\BlueSky\BlueSkyApiClient`**: BlueSky API クライアントの成功/失敗パス検証。
- **`App\Services\Discord\LogConverter`**: ログ変換ユーティリティの入出力テスト。
- **`App\Services\FeedService`**: フィード集約・生成ロジック。推奨: 入力→出力の期待値テスト。
- **`App\Services\FileInfo\FileInfoService`**: Extractor との連携を検証する統合テスト（Extractors は個別にテスト済み）。
- **`App\Adapters\AutoRefreshingDropBoxTokenService`**: トークン自動更新フロー。推奨: 期限切れトークンからの自動更新シナリオをモックで検証。

### Listeners（イベント駆動）

- **`App\Listeners\User\OnLogin`**: ログイン時のログイン履歴記録の動作確認。
- **`App\Listeners\User\OnRegistered`**: ユーザー登録時のウェルカムメール送信などの動作確認。
- **`App\Listeners\User\OnPasswordReset`**: パスワードリセット時の動作確認。
- **`App\Listeners\User\On*TwoFactor*`**: 2FA有効化・無効化時の動作確認。
- **`App\Listeners\Discord\*`**: Discord関連リスナーの動作確認。
- **`App\Listeners\Tag\*`**: タグ関連リスナーの動作確認。

### Commands（Artisanコマンド）

- **`App\Console\Commands\Article\CheckDeadLink`**: デッドリンクチェック実行時の副作用（DB更新・ジョブ投入）検証。
- **`App\Console\Commands\Article\PublishReservation`**: 予約投稿の公開処理の検証。
- **`App\Console\Commands\LangJsonExportCommand`**: 翻訳JSONエクスポート結果の検証。
- **`App\Console\Commands\MFASetupAutoRecovery`**: 2FA自動リカバリ設定の検証。
- **`App\Console\Commands\RemoveUnusedTagsCommand`**: 未使用タグ削除の検証。

実際のカバレッジを把握するには `phpunit --coverage-text`（または CI のカバレッジレポート）を実行し、網羅されていないファイルやメソッドを確認してください。

**参考**: テストの実装例は以下を参照してください：

- Twitter PKCE Service: [tests/Unit/Services/Twitter/README.md](../tests/Unit/Services/Twitter/README.md)
- フロントエンドテスト: [resources/js/**tests**/README.md](../resources/js/__tests__/README.md)

---

## テスト実装方針

- **ユニットテスト**: `tests/Unit` に配置。主にサービスなどDBに依存しない、もしくはモックに置換可能なロジックのテスト
- **機能テスト**: `tests/Feature` に配置。主にController, Repositoryを中心にデータベースに依存するテスト
- **フロントエンドテスト**: `resources/js/__tests__/` に配置。Vitest + React Testing Library を使用
