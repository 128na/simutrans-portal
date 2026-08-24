# ドキュメント規約

このリポジトリのドキュメントは「実装とテストが SSOT（唯一の真実）」を前提に、
md として残すものを次の3種類に限定する。この規約は `tools/docs-lint.mjs` で機械検証される
（`node tools/docs-lint.mjs`）。

## 原則

1. コードを読めば分かることは書かない。
2. 残すのは「意思決定の理由」（`docs/adr/`）と「過去時点の記録」（`docs/records/`）だけ。
   どちらも書いたら変更しない（リンク先パスの追従のみ例外、後述）。
3. 変化する事実（ステータス・日付・採番）は台帳に集約し、md 本文には書かない。
4. 手動維持の索引は持たない。一覧は `ls docs/records/`（日付順に並ぶ）で得る。

## 「実装が SSOT」を削除の言い訳にする前に

現在形のドキュメントを削除する前に必ず次を確認する。本移行作業で実際に、
PAK関連4ファイルと API 契約仕様1ファイルが**対応する実装が存在しない、または
実装と一致しない具体例を含む**ことが発覚した（[docs/records/2026-08-25_pak-docs-fictional-content-discovery.md](records/2026-08-25_pak-docs-fictional-content-discovery.md)、
[docs/records/2026-08-25_api-contract-spec-fictional-examples.md](records/2026-08-25_api-contract-spec-fictional-examples.md)）。

1. **対応する実装が実在するか確認する**。存在しなければ削除せず `docs/records/` へ
   日付付きで凍結する（設計提案・調査中の記録として）。
2. **文書の具体例が実装と一致するか確認する**。「実装が存在する」だけでは不十分で、
   クラス名・メソッドシグネチャ・ディレクトリ構造まで実際に突き合わせる。
3. **テストが仕様の主張を実際に検証しているか確認する**。テストファイルの存在ではなく、
   記述量に対してテストケース数が見合っているか照合する。
4. **原理的にテストが検出できない契約でないか確認する**。外部フォーマット文書や、
   自動検証のない人手同期の契約は生きた文書 allowlist の例外として認めるが、
   その場合も内容が現在の実装と一致しているかは同様に確認する。

## 分類と置き場所

### 不変記録 — docs/records/

- 命名: `YYYY-MM-DD_slug.md`（例: `2026-08-25_pak-docs-fictional-content-discovery.md`）
- 対象: 調査メモ / 作業ログ / postmortem / 実験結果 / 未実装の設計提案の凍結
- 作成後は変更しない。内容を更新したくなったら**新しい日付で新規作成**し、
  旧ファイル冒頭に `> Superseded by:` + 新記録への相対リンク、の1行だけを追記する。
  もう1つ許可される編集は、参照先ファイルが移動・削除された際の**リンクパスのみの追従**
  （主張・内容は変えず、リンク先を現在の場所や後継 ADR に向け直すだけ）。内容の書き換えは
  一切許可しない。

### 意思決定記録 — docs/adr/

- 命名: `NNNN-slug.md`（連番4桁、例: `0001-services-actions-separation.md`）
- 「なぜ」だけを書く。「どうなっているか」は実装を参照させる。
- ステータス行 `> ステータス: Accepted (YYYY-MM-DD)` が必須。
  覆すときは新 ADR を書き、旧 ADR のステータスを `Superseded by ADR-NNNN` に変える。

### 台帳 — docs/dependency-debt.md, docs/security-audit-debt.md

- 変化する事実の SSOT。行の追加・更新・削除が正規の運用。
- `dependency-debt.md` のスキーマは変更禁止（`/dependabot-maintenance` スキル互換）。
- `security-audit-debt.md` は未解消表 + 解消ログの2段構成を維持する。

### 生きた文書 — allowlist 制

- `tools/docs-policy.json` の `livingDocs` に列挙されたファイルのみ許可。現在の一覧:
  `README.md` / `CLAUDE.md` / `AGENTS.md` / `CODE_OF_CONDUCT.md` / `docs/README.md` /
  `docs/coding-standards.md`
- allowlist へ追加する場合は、追加理由を ADR として残すこと。
- 生きた文書は「常に現在を反映する義務」を負う。義務を果たせない文書は
  records 化（日付を付けて凍結）するか削除する。

## 禁止事項（lint がエラーにする）

- allowlist 外の「現在形 md」を docs/ やルートに置くこと
- `temp` / `tmp` / `draft` / `wip` という名前のディレクトリ
- `INDEX.md` / `index.md` / `TODO.md` というファイル名
- リポジトリ外への絶対パスリンク（`~/`、ドライブレター、`file://`）
- 壊れた相対リンク

## 検証

```bash
node tools/docs-lint.mjs
```

CI（`.github/workflows/docs-lint.yml`）でも同じものが走る。

定期監査は `/docs-audit` スキル（機械検証で拾えない意味的ドリフト・記載と実装の乖離を検出する）。
