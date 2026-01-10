# マイリスト機能 仕様（暫定）

最終更新: 2026-01-10 / ステータス: 草案
対象: 記事（既存記事モデルの主キー参照）
ドメイン名: mylist（マイリスト）

本書は横断的な機能追加の暫定仕様です。合意形成後に `docs/spec` 等へ移設します。

## 概要 / 目的

- ログインユーザーが複数の「マイリスト」を作成し、記事を整理・保存できるようにする。
- 各マイリストは「タイトル」「メモ」「公開・非公開フラグ」を持つ。
- マイリストに含めた各記事ごとに「メモ」記入と「並び替え（手動順序）」が可能。
- MVPではリスト作成/編集/削除、リストへの記事追加・削除、並び替え、公開リストの閲覧（読み取りのみ）までを対象。

## スコープ（MVP）

- 対象ユーザー: ログインユーザーのみ利用可能（ゲストは対象外）。
- リスト操作: 作成/編集（タイトル・メモ・公開フラグ）/削除。
- アイテム操作: 追加（記事）/削除/メモ編集/並び替え（位置付け）。
- 一覧表示:
  - 自分のマイリスト一覧（ページネーション・ソート）。
  - 選択したリストの記事一覧（ページネーション・並び替え表示）。
- 公開設定: `is_public=true` のリストは公開URLで閲覧可能（読み取りのみ、編集不可）。

## ユーザーストーリー（MVP）

- ユーザーとして、記事カードから「マイリストに追加」を選び、希望のリストへ追加したい。
- ユーザーとして、リスト内の記事にメモを残し、手動で順序を並び替えたい。
- ユーザーとして、複数のリスト（例: 調査用/公開共有用）を使い分けたい。
- ユーザーとして、公開設定したリストを他者にURL共有し、閲覧してもらいたい（編集不可）。

## 画面/UX（MVP）

- AddToMyList（island）:
  - 記事カード・詳細に「＋マイリスト」ボタンを設置。
  - 押下でモーダル表示し、リスト選択（チェックボックス/トグル）＋追加ボタン。
  - 既存リストがない場合はその場で簡易作成（タイトルのみ）を許可。
- MyListIndexPage（Blade基盤 + island）:
  - 自分のマイリスト一覧。カラム: タイトル/公開状態/作成日/更新日/操作（編集・削除）。
  - ソート: 更新日降順（デフォルト）。
  - ページネーション: 20件/ページ（初期値）。
- MyListDetailPage（Blade基盤 + island）:
  - 指定リストの記事一覧。カラム: サムネ/タイトル/投稿者/追加日/メモ/操作（削除）。
  - 所有者ビューでは非公開記事も表示（「非公開」バッジ付与、記事リンク無効化、削除/メモ編集は可能）。
  - 公開ビューでは非公開記事は表示されない（除外返却）。
  - 並び替え: ドラッグ&ドロップまたは上下ボタンで `position` を更新（可視アイテムのみ表示）。
  - メモ編集は行内編集（インライン）またはモーダル。

## データモデル（MVP）

- テーブル: `mylists` ✅
  - `id` BIGINT PK
  - `user_id` BIGINT FK（`users.id`）
  - `title` VARCHAR(120) NOT NULL
  - `note` TEXT NULL
  - `is_public` BOOLEAN NOT NULL DEFAULT FALSE
  - `slug` VARCHAR(160) NULL（公開閲覧用URL識別子。`is_public=true` のとき必須）
  - `created_at`, `updated_at` TIMESTAMP
  - インデックス: `user_id`, `is_public`, `updated_at`, ユニーク: `slug`（NULL許容）
- テーブル: `mylist_items` ✅
  - `id` BIGINT PK
  - `list_id` BIGINT FK（`mylists.id`）
  - `article_id` BIGINT FK（既存記事モデルの主キー）
  - `note` VARCHAR(255) NULL
  - `position` INT NULL（手動並び順。NULLは未設定）
  - `created_at`, `updated_at` TIMESTAMP
  - ユニーク制約: (`list_id`, `article_id`)
  - インデックス: `list_id`, `article_id`, `position`, `created_at`
- 削除ポリシー: ユーザー削除時は関連 `mylists` と `mylist_items` をcascade。記事削除時は関連 `mylist_items` も削除。

## 将来拡張データモデル（V2+）

- （現仕様で複数リスト・アイテムメモ・並び替えをMVPに取り込み済み。将来は共有リンクの詳細設定やコラボ編集等を検討。）

## API 仕様（MVP / /api/v1/mylist）✅

- 認証: Sanctum（既存運用に合わせる）。CSRF保護。
- レート制限（例）: リスト作成/更新/削除・アイテム追加/削除・並び替え各 30 req/min。
- **リソースクラス統一**: すべてのAPIレスポンスは `MyListItemResource` を使用して統一フォーマットで返却 ✅
  - 公開記事: `{ id, title, url, thumbnail, user: { name, avatar }, published_at }` を含む完全な情報
  - 非公開記事: `{ id, title }` のみの最小限の情報（セキュリティ考慮）
  - フロントエンドはUnion型で両方の構造に対応
- エンドポイント（認証必須、`/api/v1/mylist`）:
  - GET `/` — 自分のリスト一覧取得（`page`, `per_page`, `sort=updated_at:desc|asc`）。✅
  - POST `/` — リスト作成（Body: `title` 必須, `note` 任意, `is_public` 任意）。公開化する場合は `slug` を生成。✅
  - PATCH `/{mylist}` — リスト更新（`title`, `note`, `is_public`）。公開化/非公開化時には `slug` の生成/無効化を適切に処理。✅
  - DELETE `/{mylist}` — リスト削除（関連 `mylist_items` もcascade）。✅
  - GET `/{mylist}/items` — 指定リストの記事一覧（`page`, `per_page`, `sort=position|created_at:desc|asc`）。所有者ビューでは非公開記事も含めて返却（`article` が最小限の情報のみ）。✅
  - POST `/{mylist}/items` — アイテム追加（Body: `article_id`, `note` 任意）。重複は409。✅
  - PATCH `/{mylist}/items/{item}` — アイテム更新（`note`, `position`）。✅
  - DELETE `/{mylist}/items/{item}` — アイテム削除（冪等）。✅
  - PATCH `/{mylist}/items/reorder` — 並び替え（Body: `items: [{id, position}]`）。✅
- 公開閲覧（認証不要、読み取りのみ）:
  - GET `/public/{slug}` — 公開リストのメタ情報 + アイテム一覧（`page`, `per_page`, `sort`）。公開対象の記事のみ返却（非公開記事は除外）。✅
- 参考（カード一覧最適化）:
  - GET `/membership` — 指定記事ID群の「どのリストに入っているか」を取得（`ids[]=...`）。MVPでは省略可。
- レスポンス例:
  - 作成成功: `{ ok: true, list: { id, title, is_public, ... } }`
  - 重複（アイテム追加）: `{ ok: false, code: "ALREADY_IN_LIST" }`
  - アイテム一覧: `{ items: [{ id, memo, position, created_at, article: { ... } }], ... }`

## バリデーション / ビジネスルール

- `title` は必須（1〜120文字）。`note` は任意（リストはTEXT/アイテムは255文字以内）。
- リストはユーザーごとに任意個数作成可能。
- アイテムの重複登録不可（ユニーク制約で担保）。
- 並び替え: `position` は正の整数。未設定は末尾扱い。
- 公開リスト: `is_public=true` のとき `slug` 必須、読み取り専用。編集は所有者のみ。
- 追加対象記事: 公開状態の記事のみ。
- 非公開の判定: 以下のいずれかに該当する記事は「非公開」として扱う:
  - 記事のステータスが「公開」以外（下書き・予約投稿など）。
  - 記事が論理削除されている（`articles.deleted_at IS NOT NULL`）。
  - 記事の著者（ユーザー）が論理削除されている（`users.deleted_at IS NOT NULL`）。
- 可視性ルール: 所有者ビューでは非公開記事も表示（「非公開」バッジ表示、記事リンク無効化、削除/メモ編集は可能）。公開ビューでは非公開記事は除外し、`mylist_items` のデータは保持。再公開時に公開ビューへ自動再表示。
- 並び順の扱い: 非公開記事も `position` を保持。公開ビューでは可視アイテムのみ連続表示しつつ、内部順序は維持。
- メタデータ保護: 公開ビューでは非公開記事のタイトル・著者等のメタ情報は返却しない。
- 上限（初期案）:
  - 1リストあたり 200 件まで。
  - 1ユーザーあたりのリスト数は 50 まで（仮）。
  - 超過時は 422 を返却。
- 削除/更新は所有者スコープでのみ許可。非所有者は403。

## フロント実装方針

- Blade基盤 + React island（規約準拠）。
- データ受け渡し: Blade側で `<script type="application/json" id="mylist_boot">` に初期状態・設定（自分のリスト一覧など）を埋め込み、TSXでparse。
- コンポーネント:
  - `AddToMyList`（記事カード/詳細のモーダル選択 + 追加）
  - `MyListIndexTable`（自分のリスト一覧 + 編集/削除）
  - `MyListItemsTable`（リスト内アイテム一覧 + メモ編集 + 並び替え）
- 状態管理: コンポーネントローカル + 署名済みAPI呼び出し。大域状態導入は不要。

## パフォーマンス / 最適化

- アイテム一覧はページネーション必須。`position` と `created_at` にインデックスを付与。
- カード側の追加UIはモーダル内でリスト一覧をlazy loadして負荷を抑制。
- 公開リストはCDNキャッシュ（将来検討）。

## セキュリティ

- 認証必須（自分のリスト操作）。
- 権限チェック: `user_id` スコープでのみCRUD。
- CSRF 保護（Laravel標準）。
- 入力検証・公開記事確認。
- 公開リストは読み取り専用、編集系APIは401/403。

## テレメトリ / ログ

- 重要イベント: `mylist_created`, `mylist_updated`, `mylist_deleted`, `mylist_item_added`, `mylist_item_removed`, `mylist_item_reordered`。
- 失敗イベント: バリデーション/レート制限/権限エラー。

## テスト方針

- Feature（Laravel）:
  - リスト作成/更新/削除（所有者スコープ/公開フラグ/slug生成）。
  - アイテム追加/削除/メモ更新/並び替え。
  - 公開リストの読み取り（認証不要）と非公開のアクセス拒否。
  - 公開記事のみ追加可能の検証。
  - 非公開判定の3条件（記事ステータス・記事論理削除・ユーザー論理削除）の検証。
  - 非公開化後、所有者向けAPI が「非公開」フラグ付きで返すこと、公開API では除外されることの検証。
  - 上限超過の検証（リスト数・アイテム数）。
- Unit（サービス層）:
  - 並び替えロジック/重複防止/slug生成。
- Front（Vitest）:
  - `AddToMyList` の選択・追加動作。
  - `MyListItemsTable` のメモ編集・並び替え動作。
  - 非公開化後、所有者向けAPI が「非公開」フラグ付きで返すこと、公開API では除外されることの検証。
  - 「非公開」バッジ表示と記事リンク無効化の UI 動作検証。

## ロールアウト計画

### 次のステップ

1. ✅ **マイグレーション実行**: `php artisan migrate` で `mylists` と `mylist_items` テーブルを作成
2. ✅ **バックエンド実装**: API エンドポイント完了（11エンドポイント）
3. ✅ **リソースクラス統一**: `MyListItemResource` で公開/非公開記事のレスポンス形式を統一
4. ✅ **フロントエンド実装**: React islands とBlade テンプレート作成
5. ✅ **型安全対応**: Union型で公開/非公開記事の構造の違いに対応
6. ✅ **マイリスト一覧CRUD**: 動作確認完了（作成・読取・更新・削除）
7. ✅ **アイテム管理UI**: 詳細ページのメモ編集・並び替え・削除の表示実装完了
8. **記事追加機能**: AddToMyListButton の動作確認（残タスク）
9. **公開リスト機能**: 公開URLでの閲覧動作確認（残タスク）
10. **テスト実装**: Feature/Unit/Vitest テストを追加（残タスク）
11. **ステージング検証**: UX・負荷確認（公開リストの閲覧も含む）
12. **本番デプロイ**: マイグレーション → キャッシュクリア → 監視設定
13. **フィードバック収集後**: 公開ページのソーシャル共有導入等を検討

#### 最近の実装（2026-01-10）

- **リソースクラスによるAPI統一** ✅
  - `MyListItemResource` を全エンドポイントで使用
  - 公開記事と非公開記事で異なるデータ構造を返却
  - セキュリティ: 非公開記事のメタデータ保護を徹底
- **フロントエンド型安全対応** ✅
  - Union型で公開/非公開記事の両方の構造に対応
  - 型ガード関数で記事状態を判定
  - 型アサーションを使用してESLintルール準拠
- **UIの改善** ✅
  - メモ編集: 1行入力でセル内に収まる設計
  - 長いメモ: `truncate`で省略、ホバー時に全文表示
  - 非公開記事: 視覚的にわかりやすい表示（バッジ、リンク無効化）

## リスク / 代替案

- 記事削除時の参照整合性: FK制約とcascadeで担保。✅実装済み
- 並び替えの大規模更新: バルク更新APIで効率化、トランザクション使用。✅実装済み（`reorderItems`エンドポイント）

1. 複数リスト作成: MVPで導入（Yes）。
2. 公開リストの閲覧: 読み取り専用で公開URL提供（Yes）。
3. 上限: 1リスト200件/1ユーザー50リスト（案）。
4. アイテムメモ: 導入（最大255文字）。
5. 並び替え方式: `position` による手動順序、バルク更新API採用。
   削除ポリシー: ユーザー削除時は関連 `mylists` と `mylist_items` をcascade。記事削除時は関連 `mylist_items` も削除。
6. 会員限定: ログイン必須、ゲストは非対応。

### Backend（完了）✅

- Migration: `mylists`, `mylist_items` 作成（ユニーク・FK・インデックス）✅
  - `database/migrations/2026_01_10_000001_create_mylists_table.php`
  - `database/migrations/2026_01_10_000002_create_mylist_items_table.php`
- Models: `MyList`, `MyListItem` 作成（リレーション、スコープ、ソフトデリート対応）✅
  - `app/Models/MyList.php`
  - `app/Models/MyListItem.php`
- Service: リストCRUD/アイテムCRUD/並び替え/slug生成/非公開フィルタリング ✅
  - `app/Services/MyListService.php`（12+メソッド）
- Controller + Route: `/api/v1/mylist/*` 実装（Sanctum認証、権限チェック、公開記事検証）✅
  - `app/Http/Controllers/Mypage/MyListController.php`（11エンドポイント）
  - `routes/internal_api.php`（認証必須エンドポイント）
  - `routes/api.php`（公開エンドポイント）
- **Resource Layer: リソースクラスによる統一レスポンス形式** ✅
  - `app/Http/Resources/Mypage/MyListItem.php`
  - 公開記事と非公開記事で異なるデータ構造を返却
  - 公開記事: 完全な記事情報（URL、サムネイル、投稿者情報等）
  - 非公開記事: 最小限の情報（ID、"非公開記事"タイトル）のみ
  - セキュリティ考慮: 非公開記事のメタデータ保護を徹底
- Validation: Form Request 5クラス作成（型安全なバリデーション）✅
  - `app/Http/Requests/MyList/StoreMyListRequest.php`
  - `app/Http/Requests/MyList/UpdateMyListRequest.php`
  - `app/Http/Requests/MyList/StoreMyListItemRequest.php`
  - `app/Http/Requests/MyList/UpdateMyListItemRequest.php`
  - `app/Http/Requests/MyList/ReorderMyListItemsRequest.php`
- **TypeScript型定義** ✅
  - `resources/js/types/models/MyList.ts`
  - **Union型対応**: `MyListItemShow["article"]` は公開/非公開で異なる型構造
    ```typescript
    type MyListItemShow = {
      // ...
      article:
        | { id: number; title: string; url: string; thumbnail: string | null; user: {...}; published_at: string; }
        | { id: number; title: string; } // 非公開記事
    }
    ```
- コンポーネント ✅
  - `resources/js/features/mylist/AddToMyList.tsx`
  - `resources/js/features/mylist/MyListTable.tsx`
  - **`resources/js/features/mylist/MyListItemsTable.tsx` - 型安全実装** ✅
    - 型ガード関数 `isPublicArticle()` で記事の公開状態を判定
    - 公開記事: サムネイル、リンク、投稿者情報を表示
    - 非公開記事: 「非公開」バッジ表示、リンク無効化、サムネイル非表示
    - **メモ編集UI改善**: 1行入力、セル内に収まる設計（`min-w-0`、`truncate`クラス使用）
    - 並び替え（上下ボタン）
    - アイテム削除
- TypeScript型定義 ✅
  - `resources/js/types/models/MyList.ts`
- コンポーネント ✅
  - `resources/js/features/mylist/AddToMyList.tsx`
  - `resources/js/features/mylist/MyListTable.tsx`
  - `resources/js/features/mylist/MyListItemsTable.tsx`
- ページ実装 ✅
  - `resources/js/mypage/pages/MyListIndexPage.tsx`
  - `resources/js/mypage/pages/MyListDetailPage.tsx`
- Bladeテンプレート ✅
  - `resources/views/mypage/mylists.blade.php`
  - `resources/views/mypage/mylist-detail.blade.php`
- Viteエントリーポイント更新 ✅
  - `resources/js/mypage.ts` ✅
  - アイテム一覧テーブル（サムネイル/タイトル/投稿者/メモ/追加日/操作）
  - **インラインメモ編集**: クリックで編集モード、保存/キャンセルボタン
    - 1行入力でテーブルセル内に収まる設計
    - 長いメモは `truncate` で省略、ホバー時に全文表示（title属性）
  - **並び替え**: 上下ボタンで位置変更
  - **アイテム削除**: 削除ボタンで即時削除
  - **非公開記事の表示**:
    - 所有者ビュー: 非公開記事も表示（「非公開」バッジ、リンク無効化）
    - サムネイル: 非公開記事は「No Image」表示
    - 投稿者: 非公開記事は「-」表示
    - 削除/メモ編集は可能
  - 新規リスト作成（モーダル内）
  - 複数リストへの一括追加
  - 重複チェック
- **MyListIndexPage**: マイリスト一覧管理 ✅ 動作確認済み
  - テーブル表示（タイトル/公開状態/アイテム数/更新日）
  - リスト作成・編集・削除（CRUD完全動作）
  - モーダルUI（作成・編集・削除確認）
  - v2-\* デザインシステム統一（Button/Link/TextBadge/Card/Table）
  - エラー・ローディング・空状態の適切な表示
- **MyListDetailPage**: リスト内アイテム管理
  - アイテム一覧テーブル
  - インラインメモ編集
  - 並び替え（上下ボタン）
  - アイテム削除
  - 非公開記事の視覚的表示（バッジ、リンク無効化）
  - ナビゲーション（一覧へ戻る/公開ページ表示）
  - v2-\* デザインシステム統一

### Test（未実装）

- Feature テスト（Laravel）: API エンドポイント、権限、非公開フィルタリング
- Unit テスト（PHP）: サービス層ロジック、slug生成、並び替え
- Component テスト（Vitest）: React islands の動作検証

## 参考

- 規約: `docs/knowledge/project-coding-standards-20260101-knowledge.md`
- 作業フロー: `.github/copilot-instructions.md`
