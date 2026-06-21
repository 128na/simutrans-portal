# Security Audit Debt

Assurance Audit（web-security パック）で見つかった未解消のセキュリティ関連負債の台帳。
解消したら表から行を削除し、本文末尾の解消ログに移動する。「未マージPR数」ではなく
この表を棚卸し・監査の対象にする（`docs/dependency-debt.md` と同じ運用方針）。

## 監査の前提

- 実施日: 2026-06-21
- 使用パック: web-security（authz/IDOR/レート制限/入力検証/データ露出）
- **保護対象の振る舞いを管理する台帳は監査前には存在しなかった。** 本ファイルがその台帳の最初のバージョンになる。
- 今回の監査では以下は調査対象外（次回監査で扱うべき候補）: SQL/コマンドインジェクション、格納型XSS、SSRF（Twitter OAuthコールバックが候補）、セッション固定化、メール/SMS再送スパム、マスアサインメントによる権限昇格、タイミング/404-vs-403によるユーザー存在列挙。

## 未解消の負債（優先度順）

（現時点で未解消の項目はありません。次回の監査でSQL/コマンドインジェクション、格納型XSS、SSRF、セッション固定化、メール/SMS再送スパム、マスアサインメント、ユーザー存在列挙を調査対象に追加する）

## 解消ログ

| # | 項目 | 対応内容 | 解消日 |
|---|---|---|---|
| 1 | 添付ファイルのアップロードに種別/サイズ制限なし | `app/Http/Requests/Attachment/StoreRequest.php` に実運用データ（添付ファイル2,573件の拡張子・サイズ実績）に基づく拡張子アローリスト（png/jpg/jpeg/jfif/ppm/zip/7z/pak/tab/txt/pdf）と1GBの上限を追加（`Illuminate\Validation\Rules\File::extensions()->max()`）。`.php`/`.exe`等の拒否、サイズ超過の拒否、許可拡張子（.pak/.tabを含む）の許可を検証する単体・機能テストを追加 | 2026-06-21 |
| 2 | `/mcp/user` に `verified` ミドルウェアがない | `routes/ai.php` の `/mcp/user` ルートに `verified` ミドルウェアを追加。`tests/Feature/Mypage/TokenControllerTest.php` に未認証メールユーザーが403で拒否されることを検証するテスト（`test_mcp_rejects_unverified_user`）を追加 | 2026-06-21 |
| 3 | `ControllOption::isRestrict()` がDB行欠落時にフェイルオープンする | `app/Models/ControllOption.php::isRestrict()` を、行が存在しない場合は安全側に倒して制限する（fail-closed）よう修正。`tests/Feature/Controllers/Auth/RegisterControllerTest.php` に `invitation_code` の設定行が存在しない場合に403になることを検証するテストを2件追加（招待ページ表示・登録の双方）。既存のFeatureテストは `ControllOptionsSeeder` で全キーが常にシードされるため影響なし | 2026-06-21 |
| 4 | Redirect削除の所有権チェック（`RedirectController::destroy`）が未テスト | `tests/Feature/Controllers/Mypage/RedirectController/DestroyTest.php` を新規作成。本人による削除成功、未ログイン時の302リダイレクト、他人のRedirectは403で削除されないことの3パターンを検証 | 2026-06-21 |
| 5 | 記事POST更新の所有権チェック（`EditController::update`）が未テスト | `tests/Feature/Controllers/Mypage/Article/UpdateTest.php` に `test_他人の記事は更新できない` を追加。他人がPOST更新を試みると403になり、記事のtitleが変更されないことを検証 | 2026-06-21 |
| 6 | ログインレート制限・2FAバイパス防止が未テスト | `tests/Feature/Controllers/Fortify/LoginTest.php` に6回目のログイン失敗で429になることを検証するテストを追加。`tests/Feature/Controllers/Auth/TwoFactorControllerTest.php` に2FA有効ユーザーがパスワードのみではログイン完了せず（`two_factor: true` を返し未認証のまま）、正しいOTPコード入力後にのみ認証されることを検証するテストを追加 | 2026-06-21 |
| 7 | 未公開記事のダウンロード/コンバージョンゲート・マイリスト非公開スラッグのアクセス制御が未テスト | テスト追加の過程で**実バグを発見・修正**：`app/Http/Controllers/Pages/Article/DownloadController.php::conversion()` は `Gate::allows()` の結果をカウント処理の有無にしか使っておらず、未公開記事でも `contents->link` が設定されていれば常にリダイレクトしていた（未公開記事のアドオンリンクが誰でも閲覧可能だった）。`Gate::denies()` で先に404を返すよう修正。`tests/Feature/Controllers/Pages/Article/DownloadControllerTest.php`（新規）でダウンロード/コンバージョン双方の未公開ケースを検証。マイリストは `tests/Feature/Controllers/Pages/PublicMyListController/ShowTest.php`・`ShowPublicTest.php`（新規）で非公開slugアクセスが404になることを確認（既存実装は元から正しく、テストのみ追加） | 2026-06-21 |
| 8 | `TagPolicy::toggleEditable` が呼び出し元なし（デッドコード） | ユーザーに確認の上、削除を選択。アプリ・テスト双方に呼び出し元がないことを再確認してから `app/Policies/TagPolicy.php` から削除 | 2026-06-21 |
| 9 | 添付ファイル削除の所有権テストがSPOF（Strongなテストが1件のみ） | `tests/Feature/Controllers/Mypage/AttachmentController/DestroyTest.php` に `test_他人のファイルは削除されずに残る` を追加し、403レスポンスのアサーションとDB未削除のアサーションを別テストに分離。単一テストの破損が唯一の防御線にならないようにした | 2026-06-21 |

<!--
運用メモ:
- 各項目を実装したら、対応する行をこの表から削除し、上記「解消ログ」セクションに
  「# | 項目 | 対応内容 | 解消日」の形式で1行追記する。
- 新しい監査を実施した場合は「監査の前提」の実施日・調査対象外リストを更新する。
-->
