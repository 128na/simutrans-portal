# Test Inventory — 2026-09-25

`test-inventory` スキル（テスト棚卸し: Keep / Merge / Rewrite / Delete / Ask）の試行。
対象はバックエンド `tests/`（PHPUnit、219ファイル・約20,000行）、基準コミット `091bb786`。
フロント（vitest、62ファイル）は今回ベースライン未計測。
**本記録は方針のみ。テストの変更は別セッションで行う（本セッションではファイル未変更）。**

全件の台帳化はしておらず、機械的シグナル（常時 skip、ファイル別実行時間、正規化 diff）で
候補を絞って中身を読んだ。

## ベースライン

| 項目 | 値 |
|---|---|
| 結果 | 1103 passed / 7 skipped（2675 assertions、緑） |
| 実行時間 | 約67秒（`php artisan test`、MySQL `testing` 接続） |
| 最も遅いファイル | `Feature/Requests/Article/UpdateRequestTest.php` 3.5s（40件）、`StoreRequestTest.php` 2.5s（42件） |

総評: 長くメンテされている割に速度は問題なし。主な負債は **2025-12〜2026-01 から残り続けている常時 skip 7件**。
長寿 repo らしく「仕様が変わったのにテストが skip で化石化」のパターンが出た。

## 方針（別セッションで実施）

### A. Delete — 仕様消滅・本体の無い skip テスト 4件

| テスト | skip 理由 | 判断 |
|---|---|---|
| `Unit/Services/MyListServiceTest.php::test_update_list_generates_slug_when_changing_to_public` | Slug is generated at list creation, not at update | 仕様消滅。本体は skip のみ |
| `Unit/Services/MyListServiceTest.php::test_update_list_clears_slug_when_changing_to_private` | Slug is generated at list creation | 同上 |
| `Feature/Controllers/Mypage/MyListController/UpdateTest.php::test_generates_slug_when_changing_to_public` | 同上 | 同上 |
| `Unit/Actions/SendSNS/Article/ToMisskeyTest.php::test_throws_exception_for_unsupported_notification` | Cannot instantiate abstract | 本体は skip のみ。コメント自身が「他のテストで十分」と記載 |

いずれも 2026-01-11（`9389aa124`）/ 2025-12-06（`b53974b2f`）から skip のまま。
「slug は作成時に生成」という現行仕様を守るテストが作成側にあるかは削除時に確認し、無ければ作成側に1件足す。

### B. Delete — コメントアウトされた実装詳細テスト 1件

- `Feature/Console/Commands/Article/PublishReservationTest.php::test_command_signature_is_correct`
  - skip 理由「RefreshDatabase実行してもレコードが残るのでスキップ」（2025-12-07 `5be71807a`）。本体はコメントアウト済み。
  - 検証内容はコマンド名（`article:publish-reservation`）で実装詳細。スケジューラ登録や実行経路で守る方が筋。

### C. Rewrite — 重要な振る舞いが skip で未検証のまま 2件（最優先）

- `Unit/Listeners/User/OnLoginTest.php::test_creates_login_history_and_sends_notification_on_new_login`
- `Unit/Listeners/User/OnLoginTest.php::test_logs_login_to_audit_channel`
- skip 理由: `isNewLogin()` がバックトレースを見るため Unit では常に false。
- **これは削減候補ではなく保証の穴**。新規ログイン時のログイン履歴・通知・監査ログというセキュリティ上重要な振る舞いが、
  どのテストでも検証されていない（`LoginHistoryRepository` の取得テストのみ存在）。
- 方針: 実際のログイン経路を通す Feature テストに書き直し、Unit 側の skip 2件は削除。
  バックトレース依存の `isNewLogin()` 自体をテスト可能な形に変えるかは本体の設計判断として併せて検討。
  棚卸しの範囲を超えるため、[assurance-audit] の対象として扱う。

### D. Ask（低優先）— 記事 Store/Update リクエストテストの重複

- `Feature/Requests/Article/StoreRequestTest.php` と `UpdateRequestTest.php` は正規化 diff 約20行のほぼ同一（計82ケース）。
- ただし本体の `StoreRequest` / `UpdateRequest` もルールを個別に持っており（差分: post_type 必須、title の unique 除外 ID、
  update 専用フラグ）、各エンドポイントが実際にルールを強制することの検証としては重複に意味がある。
- 共通ルールを `BaseRequest` へ寄せる本体リファクタをする時に、共通 DataProvider（trait）へ統合する。単独では着手しない。

**決定・実施**: `BaseRequest` への集約は既に完了済みと判明したため、単独でも着手可能と判断。
`UpdateRequestTest` から投稿種別ごとのルール検証（35ケース）を削除し、Update 固有差分
（`post_type` 必須の有無・`title` unique の自記事除外・`without_update_modified_at`/
`follow_redirect`）と代表ケース1件に絞った。投稿種別ルールの網羅は `StoreRequestTest` に残す。
[#584](https://github.com/128na/simutrans-portal/pull/584)

## Keep と判断した主なもの

- 上記以外の 1103 件は今回のシグナルでは問題なし（ファイル別の最長 3.5s、他の skip なし）。

## 再発防止（別セッションで AGENTS.md への追記を検討）

- 仕様変更でテストが意味を失ったら `markTestSkipped` で残さず、同じ変更で削除する（skip は一時的な措置に限り、理由と期限/Issue を書く）。
- 技術的理由で Unit にできない重要な振る舞いは、skip ではなく Feature テストで守る。
