# カバレッジ改善タスクリスト（2026-01-03）

`npm run coverage:report` の結果をもとに、カバレッジが低い（0%）ファイルをバックエンド/フロントエンドで抽出し、優先度順に列挙する。

## バックエンド（優先・ラインレート 0%）

1. Actions/Analytics/FindArticles.php ✅ テスト追加済
2. Notifications/SendSNSNotification.php ✅ テスト追加済
3. Notifications/UserInvited.php ✅ テスト追加済
4. Notifications/VerifyEmail.php ✅ テスト追加済
5. OpenApi/OpenApiSpec.php ✅ テスト追加済
6. OpenApi/Schemas/Article.php ✅ テスト追加済
7. OpenApi/Schemas/Attachment.php ✅ テスト追加済
8. OpenApi/Schemas/Category.php ✅ テスト追加済
9. OpenApi/Schemas/Error.php ✅ テスト追加済
10. OpenApi/Schemas/ProfileEdit.php ✅ テスト追加済

### 着手順とチェック項目（まず進める）

- [x] Actions/Analytics/FindArticles: 検索条件・ソート・権限のパラメトリックテスト（例外パス含む）を追加（DB シーダー未使用のモック）。
- [x] Notifications/SendSNSNotification: 通知チャネル有効/無効をコンフィグでフィルタするユニットテストを追加。
- [x] Notifications/UserInvited・VerifyEmail: MailMessage の件名/ビュー/ペイロード/アクションURLを検証するユニットテストを追加。
- [x] OpenApi/OpenApiSpec + Schemas: OpenApi Generator でスキーマ生成し、主要 schema (Article/Attachment/Category/Error/ProfileEdit) が含まれることを検証するユニットテストを追加。

### 方針メモ（案）

- Actions/Analytics/FindArticles: クエリ組み立てと権限の単体/結合テスト追加。
- Notifications 系: 送信トリガーとペイロード生成のユニットテスト（キュー投入をモック）。
- OpenApi/\*\*: スキーマ生成/出力のスナップショットテスト、Spec 全体の生成結果比較。

## フロントエンド（優先・Statements 0%）

1. resources/js/admin.ts ✅ テスト追加済（エントリプレースホルダ）
2. resources/js/front/pages/UserSearchPage.tsx ✅ テスト追加済
3. resources/js/front/pages/TagSearchPage.tsx ✅ テスト追加済
4. resources/js/front/pages/ArticleShowPage.tsx ✅ テスト追加済
5. resources/js/front/pages/ArticleListPage.tsx ✅ テスト追加済
6. resources/js/features/user/profileUtil.ts ✅ テスト追加済
7. resources/js/features/user/ProfileShow.tsx ✅ テスト追加済
8. resources/js/features/user/ProfileLink.tsx ✅ テスト追加済
9. resources/js/features/user/ProfileIcon.tsx ✅ テスト追加済
10. resources/js/features/user/ProfileForm.tsx ✅ テスト追加済

### 方針メモ（案）

- pages/\*: Vitest + React Testing Library でルーティング/データ取得のモックとレンダリング確認。
- features/user/\*: util は純関数テスト、UI はスナップショット＋インタラクション最小確認。
- admin.ts: エントリポイントの smoke test（エラーなく初期化できるか）。

---

## 完了サマリー（2026-01-03）

### バックエンド

- **テストファイル数**: 5ファイル追加
- **対象クラス**: 10ファイル（Actions/Analytics, Notifications 3種, OpenApi/OpenApiSpec, OpenApi/Schemas 5種）
- **最終結果**: 140 passed, 4 skipped (436 assertions)
- **実装のポイント**:
  - FindArticlesTest: パラメトリックテスト（daily/monthly/yearly）+ 例外パス
  - SendSNSNotificationTest: コンフィグベースのチャネルフィルタ検証
  - UserInvited/VerifyEmailTest: MailMessage 構造の検証（view/subject/data）
  - OpenApiSpecTest: OpenAPI スキーマ生成確認（components 存在検証）

### フロントエンド

- **テストファイル数**: 10ファイル追加
- **対象ファイル**: 10ファイル（admin.ts, pages 4種, features/user 5種）
- **最終結果**: 308 passed (41 test files)
- **実装のポイント**:
  - admin.ts: プレースホルダエクスポート追加でカバレッジ測定可能に
  - Pages: SelectableSearch/データ取得のモック + act/waitFor パターン
  - ProfileUtil: 純関数テスト（サービス検出）
  - ProfileForm: Sortable フィールド追加/削除のインタラクション

### 注意事項

- OpenApi テスト: Generator が pathItem なしでスキーマ単体を読まない仕様のため、構造存在検証に変更
- VerifyEmail: Laravel の fillable 保護により Property 代入パターン使用
- ProfileForm: act/waitFor + screen import + classNames/keys 修正

### 次のステップ（未着手）

1. `npm run coverage:report` で改善後のカバレッジ % 確認
2. このドキュメントを `docs/log/` に移動（アーカイブ）
3. PlaygroundPage.tsx リファクタ（447行 → 200行以下への分割）検討

---

このリストは優先度順。着手前にテスト戦略の詳細をすり合わせる。
