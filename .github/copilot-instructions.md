## Simutrans Portal — Copilot / AI エージェント向けガイド

このリポジトリで AI コーディングエージェントがすぐに作業に入れるように、実用的な注意点を短くまとめています。

## プロジェクト概要

- バックエンド: Laravel (PHP 8.3+, Laravel 12)。主要入口: `artisan` と `composer.json`。
- フロントエンド: React + TypeScript + Vite（旧 README の Quasar/Vue 表記は古い）。フロントエンドのルート: `resources/js/`。
- DB: MySQL（`.env.example` を参照）。テスト: PHPUnit / `php artisan test`。CI カバレッジ: `coverage.128-bit.net`。

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

- `routes/` — `web.php`, `api.php`, `internal_api.php` にルーティング定義。コントローラの境界がわかる。
- `app/` — Laravel のアプリコード（`Models`, `Repositories`, `Services`, `Http/Controllers`）。ビジネスロジックは `Repositories` / `Services` に分離されることが多い。
- `app/Http/Controllers/` — コントローラーは機能別に整理されている：
    - `Auth/` — 認証関連（LoginController, RegisterController, TwoFactorController, PasswordController）
    - `Pages/` — 公開ページ（TopController, UserController, TagController, CategoryController, SocialController, DiscordController）
        - `Pages/Article/` — 記事関連（IndexController, ShowController, DownloadController, PakController）
    - `Mypage/` — マイページ（DashboardController, ProfileController, AnalyticsController, AttachmentController, TagController, RedirectController, InviteController）
        - `Mypage/Article/` — 記事管理（IndexController, CreateController, EditController）
    - `Admin/` — 管理画面（OauthController）
    - `RedirectController` — 旧URL→新URLリダイレクト・固定リダイレクト
- `resources/js/` — フロントエンドソース。エントリ: `front.ts`, `mypage.ts`。
  - `components/` — 再利用可能なUIコンポーネント
    - `ui/` — 小さなUIパーツ（Button, Input, Modal等）
    - `layout/` — レイアウト系コンポーネント（Header, Pagination等）
    - `form/` — フォーム関連コンポーネント
  - `features/` — 機能別コンポーネント
    - `articles/` — 記事関連機能
    - `tags/` — タグ機能
    - `analytics/` — アナリティクス
    - `attachments/` — 添付ファイル
    - `user/` — ユーザー機能
  - `front/` — フロントページ用コンポーネント
  - `mypage/` — マイページ用コンポーネント
  - `hooks/` — カスタムフック
  - `lib/` — ユーティリティ・ヘルパー
  - `types/` — 型定義（API 変更や props 変更時に更新が必要）
  - `utils/` — 汎用関数
  - `__tests__/` — テストファイル
- `resources/views/` — Blade テンプレート
  - `layouts/` — レイアウトテンプレート
    - `base.blade.php` — 共通ベース
    - `front.blade.php`, `mypage.blade.php`, `admin.blade.php` — 各エントリポイント
  - `components/` — 再利用可能なUIコンポーネント
    - `ui/` — 小さなUIパーツ（link, session-message等）
    - `layout/` — レイアウト系（header等）
    - `partials/` — 部分テンプレート（ga, meta-tags等）
  - `pages/` — 公開ページテンプレート
    - `top/` — トップページ
    - `users/` — ユーザーページ
    - `tags/` — タグページ
    - `categories/` — カテゴリページ
    - `pak/` — Pakページ
    - `search/` — 検索ページ
    - `social/` — ソーシャルページ
    - `discord/` — Discordページ
    - `announces/` — お知らせページ
    - `show/` — 記事詳細ページ
    - `static/` — 静的ページ
  - `auth/` — 認証画面（login, register, password reset等）
  - `mypage/` — マイページ
  - `admin/` — 管理画面
  - `emails/` — メールテンプレート
  - `errors/` — エラーページ
- `public/`, `public/build` — コンパイル済みアセット。手動編集しないこと。

## フロントエンド特有の注意点

- React + TypeScript + Vite 構成。`tsconfig.json` と `vite.config.ts` の設定に注意。

- コンポーネントは小さく保ち、ビジネスロジックは `features/` や `state/` に置く流れ。
- HTTP クライアントは `axios` を利用（`resources/js/apps/*`）。エラー処理は `state/useAxiosError.ts` を参照。
- UI 変更時は `resources/js/types/*.d.ts` の更新を忘れずに。型を更新したら `npm run build` でビルド確認。
- **ロギング方針**: 
  - `console.log`, `console.error`, `console.warn` の直接使用は禁止（ESLint の `no-console` ルールで検出）
  - 開発時のデバッグには `resources/js/utils/logger.ts` の logger を使用
  - logger は開発環境でのみコンソール出力、本番環境では何も出力しない
  - 例外: `logger.ts` 自体と `vite.config.ts`（ビルドツール）のみ console の使用を許可

### 型定義の配置ルール

型定義は `resources/js/types/` 配下に体系的に整理されています：

- **`types/models/`** - Laravel モデルに対応する TypeScript 型
  - `Article.ts`, `User.ts`, `Tag.ts`, `Category.ts`, `Attachment.ts` など
  - 公開ページ用（`Show`）とマイページ用（`MypageEdit`, `MypageShow`）を区別
- **`types/api/`** - API リクエスト/レスポンスの型
  - `article.d.ts`, `user.d.ts`, `tag.d.ts` など
  - エンドポイントごとに整理
- **`types/utils/`** - ユーティリティ型
  - `response.d.ts` - `ApiResponse<T>`, `PaginatedResponse<T>`, `ValidationError`, `ErrorResponse`
  - `pagination.d.ts` - ページネーション関連型
- **`types/components/`** - コンポーネント共通Props型
  - `ui.d.ts` - UI コンポーネント型
  - `form.d.ts` - フォーム関連型
- **`types/index.d.ts`** - グローバル型定義と後方互換性レイヤー

**新しいコードでの使用方法:**
```typescript
// 明示的にインポート（推奨）
import type { ArticleList, UserShow } from '@/types/models';
import type { ArticleListResponse } from '@/types/api';
import type { PaginatedResponse } from '@/types/utils';

const [articles, setArticles] = useState<ArticleList[]>([]);
```

**既存コードとの互換性:**
```typescript
// グローバル名前空間での使用（後方互換性のため残されている）
const [articles, setArticles] = useState<Article.List[]>([]);
const [user, setUser] = useState<User.Show>();
```

両方の記法がサポートされていますが、新しいコードでは明示的なインポートを推奨します。

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

- ルートに `credential.json` があるため、どの環境向けかを確認すること。
- 多くのフロントエンドファイルは `.tsx`（TypeScript）なので、props を変更する場合は `types/` を必ず更新。

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

- フロントエンドの UI バグ: `resources/js/components` と `resources/js/features` を確認。
- コントローラーの場所: `Auth/`, `Pages/`, `Mypage/`, `Admin/` ディレクトリで機能別に分類されている。
- API の契約不一致: `routes/api.php` と該当コントローラ (`app/Http/Controllers`) を確認。
- CI や静的解析エラー: ローカルで `composer run stan` と `composer run pint` を実行して再現する。

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
- **ドキュメント:** README や該当する型定義、API の説明を必要に応じて更新する。
- **PR 説明:** 変更内容、レビュアが落とすべきコマンド（例: `npm run build`, `composer run stan`）、マイグレーションや手動手順があれば記載する。
- **CI の確認:** CI がグリーン（`composer run all` 相当のチェックを含む）になるまでマージしない。

## 未実装のテスト候補

以下はリポジトリ走査により「テストで直接参照されていない」可能性が高いクラス／機能の候補です。自動検出のため誤検知があり得ます。優先度は概ね重要度と外部依存の有無で分けています。

- **`App\Services\MarkdownService`**: Markdown の変換・サニタイズ。推奨テスト名例: `test_render_basic`, `test_escape_xss`, `test_links_and_images`。
- **`App\Services\Twitter\TwitterV2Api`**: Twitter API クライアント。推奨: 成功・HTTPエラー・例外処理のモック化テスト。
- **`App\Services\Twitter\PKCEService`**: PKCE/OAuth トークン管理。推奨: `test_create_pkce`, `test_refresh_token_error`。
- **`App\Services\Misskey\MisskeyApiClient`**: Misskey クライアント。推奨: API レスポンスの正規化テスト。
- **`App\Services\FeedService`**: フィード集約・生成ロジック。推奨: 入力→出力の期待値テスト。
- **`App\Services\FileInfo\FileInfoService`**: Extractor との連携を検証する統合テスト（Extractors は個別にテスト済み）。
- **`App\Adapters\AutoRefreshingDropBoxTokenService`**: トークン自動更新フロー。推奨: 期限切れトークンからの自動更新シナリオをモックで検証。
- **`App\Services\BlueSky\BlueSkyApiClient`**: BlueSky API クライアントの成功/失敗パス検証。
- **`App\Services\Discord\LogConverter`**: ログ変換ユーティリティの入出力テスト。
- **`App\Listeners\User\OnLogin`**, **`OnRegistered`** 等の一部リスナー: イベント → リスナーの動作確認テスト。
- **`app/Console/Commands/*`** のコマンド類: 実行時の副作用（DB 更新・ジョブ投入など）を検証する Feature テスト。

実際のカバレッジを把握するには `phpunit --coverage-text`（または CI のカバレッジレポート）を実行し、網羅されていないファイルやメソッドを確認してください。

---

## テスト実装方針

- ユニットテスト: `tests/Unit` に配置。主にサービスなどDBに依存しない、もしくはモックに置換可能なロジックのテスト
- 機能テスト: `tests/Feature` に配置。主にController, Repositoryを中心にデータベースに依存するテスト
