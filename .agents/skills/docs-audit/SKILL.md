---
name: docs-audit
description: >-
  ドキュメントのドリフト監査。「docs監査」「ドキュメント棚卸し」「ドリフト検査」
  「docsレビュー」「ドキュメントと実装の乖離を調べて」等の依頼で必ず使用。
  機械検証(docs-lint)で拾えない意味的ドリフトを検出し、consistency-debt.md への
  行追加と docs/records/ への監査レポート出力を行う。
---

# docs-audit

自律性: 読み取り・docs-lint 実行・監査レポート（records）作成・consistency-debt.md への
行追加は確認不要。生きた文書の書き換え・records への Superseded 追記・ファイル削除は
提案してユーザーの承認を得てから行う。

## 手順

1. `node tools/docs-lint.mjs` を実行する。エラーがあればまず修正を提案する
   （機械検証を先にパスさせてから意味的監査に進む）。
2. 生きた文書の意味的ドリフト検査: `tools/docs-policy.json` の `livingDocs` 各文書について、
   - 記載コマンドの実在（package.json / pyproject.toml / composer.json 等と突合）
   - 記載パスの実在
   - 記述された挙動と実装のスポットチェック（乖離が疑われる箇所は該当実装を読んで確認）
3. 台帳の棚卸し:
   - `docs/dependency-debt.md`: 各行の Revisit condition が成立していないか（必要なら Web 検索）
   - `docs/consistency-debt.md`: 残行の生存確認（既に直っていないか）
4. records の世代並立検出: `docs/records/` を日付順に走査し、同一テーマで Superseded
   ラベルのない複数世代を列挙する。最新版以外へのラベル追記を提案する。
5. allowlist 縮小提案: 更新義務を果たせていない生きた文書があれば、
   records 化（日付を付けて凍結）または削除を提案する。
6. spec 相当の文書を削除提案する前の確認（[ADR-0002](../../../docs/adr/0002-verify-test-coverage-before-deleting-spec.md)）:
   「実装が SSOT だから削除可能」と判断する前に、①対応する実装が実在するか
   ②テストが仕様の主張を実際に検証しているか（仕様の記述量に対しテストケース数が
   見合っているか、名ばかりのテストでないか）③外部フォーマット文書・自動検証のない
   人手同期契約に該当しないか、を確認する。いずれか1つでも疑わしい場合は削除を
   提案せず、records への凍結か living doc 例外化を提案する。
7. 出力:
   - 監査レポートを `docs/records/YYYY-MM-DD_docs-audit.md` として作成する
     （不変記録なので索引不要、`ls` で時系列に並ぶ）
   - 要対応のドリフトは `docs/consistency-debt.md` に CD-NN 行として追加する
   - チャットに件数サマリ（lint error/warn、新規 CD、提案数）を報告する
