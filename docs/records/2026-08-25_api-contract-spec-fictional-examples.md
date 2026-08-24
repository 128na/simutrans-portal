# api-contract-typescript-types-spec.md の具体例も架空だった件

[2026-08-25_pak-docs-fictional-content-discovery.md](2026-08-25_pak-docs-fictional-content-discovery.md)
と同時期の調査で、`docs/spec/api-contract-typescript-types-spec.md` も同様に
具体例の大半が実装と一致しないことが判明した。ただし PAK 関連4件と異なり、
**文書の核となる決定（OpenAPI を SSOT とし TypeScript 型は手動管理する）自体は
実態と一致していた**。

## 突き合わせ結果

| 記述内容 | 実際の実装 |
| --- | --- |
| `namespace ArticleApi { interface CreateRequest ... }` という namespace 構造 | 実際の `resources/js/types/api/article.d.ts` はフラットな `export interface ArticleSaveRequest` 等（namespace を使わない） |
| OpenAPI 定義を `/** @OA\Post(...) */` の inline docblock で記述 | 実際は `#[OA\Schema(...)]` という PHP 8 Attribute + 専用 Schema クラス（`app/OpenApi/Schemas/Article.php`） |
| `types/__tests__/api.test.ts` という型テストファイル | 存在しない |
| `npm run type` という型チェックスクリプト | `package.json` に存在しない（`format:check` はあるが `type` は無い） |
| 「OpenAPI 仕様が SSOT、TypeScript 型は手動管理」という決定 | **実態と一致**。`resources/js/types/` は生成マーカーのない手書きファイルで、生成ツールの依存もない |

## 対応

決定部分のみ [docs/adr/0003](../adr/0003-typescript-types-manual-sync.md) として抽出し、
具体例（namespace パターン・inline docblock・型テスト・npm スクリプト）を含む
元ファイル `docs/spec/api-contract-typescript-types-spec.md` は削除した。

ADR-0003 に明記した通り、「手動管理された型が OpenAPI と実際に同期しているか」を
検証する自動テスト・CI は存在しない。この文書が架空の具体例を含んだまま長期間
気づかれなかったのは、この検証不在と無関係ではない可能性がある。
