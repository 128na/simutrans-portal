# ADR: Services と Actions の責務を「技術的関心事」と「ユースケース」で分離する

> ステータス: Accepted (2025-11-24)
> 由来: `docs/knowledge/architecture-services-actions-20260103-knowledge.md` ほか関連4ファイル（2,177行）を統合

## 背景と問題

`app/Services/` と `app/Actions/` のどちらに新しいクラスを置くべきか、境界が曖昧だと
ビジネスロジックが Service に混入したり（例: `ArticleService::createArticle()` が
バリデーション・DB保存・複数ユースケースを1クラスに抱える）、逆に単純な外部API
ラッパーが Action として実装されたりして、レイヤーの見通しが悪くなる。

## 決定

以下の基準で配置を分離する。

| 観点 | Services | Actions |
| --- | --- | --- |
| 責務 | 技術的関心事（外部API連携、インフラ、汎用ユーティリティ） | ビジネスの関心事（1つの具体的ユースケース） |
| 状態 | ステートレス、入力→出力が一定 | ドメイン固有のビジネスルール・状態遷移を含む |
| 呼び出し元 | 複数のドメイン・複数の Action から再利用される | Controller から直接呼ばれる想定 |
| 単位 | 機能単位（`MarkdownService`, `TwitterV2Api` 等） | 1クラス = 1ユースケース（`StoreArticle`, `Registration` 等） |

判断フロー:

```
外部APIやインフラと通信する？          Yes → Services/ExternalApi/ or Services/Infrastructure/
複数のドメインで再利用される？          Yes → Services/Utility/
特定のユースケースを表現する？          Yes → Actions/{Domain}/
```

命名規則:
- Services: `{機能名}Service`（例: `MarkdownService`）、外部API連携は `{サービス名}ApiClient` / `{サービス名}Api`
- Actions: 動詞で始める（例: `StoreArticle`, `Registration`）か `{動詞}{対象}Action`

```php
// ✅ Service: 技術的関心事、ステートレス
class MarkdownService
{
    public function toEscapedHTML(string $markdown): string { /* ... */ }
}

// ✅ Action: 1ユースケース、ビジネスルールを含む
class StoreArticle
{
    public function __invoke(User $user, array $data): Article
    {
        if (!$user->canCreateArticle()) {
            throw new UnauthorizedException();
        }
        // ...
    }
}

// ❌ Anti-pattern: Service にビジネスロジックと複数ユースケースが混在
class ArticleService
{
    public function createArticle(array $data) { /* バリデーション+DB保存+... */ }
    public function updateArticle(Article $article, array $data) { /* ... */ }
}
```

テスト: Services はユニットテスト（`tests/Unit/Services/`、外部依存はモック化）、
Actions は機能テスト（`tests/Feature/Actions/`、DBを使いビジネスルールを検証）。

よくある質問への回答:
- Service と Action の両方に当てはまる場合 → ユースケースを表現する側面が強ければ Action。
  Action 内部から Service を呼ぶ構成にする。
- Controller に直接ロジックを書いてよいか → 不可。薄いコントローラーを維持し Action に委譲する。
- Service から別の Service を呼んでもよいか → 可能。ただし依存が深くなりすぎないよう注意し、
  循環参照は避ける。多数の Service を組み合わせる必要が出てきた場合は Action への切り出しを検討する。
- Action から別の Action を呼んでもよいか → 可能。既存コードでも実例がある
  （例: `StoreArticle` 内から `SyncRelatedModels` を呼び出す構成）。
- 既存コードの移動要否 → 2025-11-24 時点の分析では概ね適切に配置されており、
  大規模な移動は不要と判断した（`Services/Front/MetaOgpService` のみ要検討として保留）。

## 検討した代替案と却下理由

- 案A（Service/Action を分けず全て Service に統合）:
  → ビジネスロジックと技術的関心事が同一クラスに混在し、テストの焦点が曖昧になる
    （`ArticleService` のアンチパターンとして実際に問題視されていた）。却下。
- 案B（全て Action として実装し Service 層を廃止）:
  → 外部API連携等の技術的関心事が複数のユースケースから重複実装される。却下。

## 影響・トレードオフ

- 新しいクラスを追加するたびに判断コストが発生する（ただし表2つで機械的に判断可能な設計にした）。
- 判断基準を1本のADRに集約したことで、以前あった4種の派生ドキュメント
  （クイックリファレンス・判断フローチャート・コードレビューチェックリスト・索引、
  計2,177行）は本ADRへの統合により廃止した。レビュー時にチェックリストが必要になった場合は、
  本ADRの表を参照するか、必要性が具体化した時点で再度ドキュメント化を検討する。
- 再検討のトリガー: `Domain/` レイヤー（純粋なドメインロジック層）を導入する場合、
  本ADRの2層構造を3層構造へ改訂する新ADRが必要になる。
