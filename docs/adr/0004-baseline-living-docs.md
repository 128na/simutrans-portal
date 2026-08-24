# ADR: ドキュメント基盤移行時のベースライン生きた文書を allowlist に登録する

> ステータス: Accepted (2026-08-25)

## 背景と問題

ドキュメンテーション基盤移行（`docs/README.md` 導入、[ADR-0001](0001-services-actions-separation.md) 以降）に伴い、
`tools/docs-policy.json` の `livingDocs` allowlist を新設した。`docs/README.md` は
「allowlist へ追加する場合は、追加理由を ADR として残すこと」と定めているが、
移行時に登録した6件のベースライン（`README.md` / `CLAUDE.md` / `AGENTS.md` /
`CODE_OF_CONDUCT.md` / `docs/README.md` / `docs/coding-standards.md`）自体に
その理由を記録していなかった。本 ADR で遡って理由を残す。

## 決定

以下を `livingDocs` の初期ベースラインとして登録する。

- `README.md` — プロジェクト概要。常に現在のセットアップ・主要コマンドを反映する必要がある
- `CLAUDE.md` / `AGENTS.md` — AI エージェント向け作業指示。プロジェクトの現状（コマンド・
  アーキテクチャ・規約）を常に反映する必要がある
- `CODE_OF_CONDUCT.md` — GitHub の Community Standards 上、コミュニティ運営方針を示す
  現在形の文書として機能する必要がある（意思決定の理由でも過去の記録でもなく、常に
  現在有効な行動規範を示す文書という性質上、生きた文書に分類する）
- `docs/README.md` — ドキュメント規約そのもの。規約が変われば直接更新する
- `docs/coding-standards.md` — 運用・コーディング規約。Git/デプロイ/API/DB規約は
  実装から読み取れず、かつ本質的に「常に現在を反映すべき」性質を持つため
  （spec 廃止の対象にはならない）

## 検討した代替案と却下理由

- 案A（6件も含めて個別に ADR を作らず、移行実施記録に含めるだけにする）:
  → `docs/README.md` 自身が定める規約に反する状態を放置することになるため却下。
    後から追加する2件目以降の生きた文書の追加者が「ベースラインは無審査だったのに
    自分だけ ADR が必要なのか」と疑問を持つ余地をなくすため、本 ADR で明文化する。

## 影響・トレードオフ

- 今後 `livingDocs` に7件目以降を追加する際は、本 ADR のような理由記載が必須となる
  （`docs/README.md` の既存規定どおり）。
