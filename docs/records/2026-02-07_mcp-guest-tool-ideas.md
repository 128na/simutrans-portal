# MCP ゲスト向けツール候補

ゲスト（未ログイン）向けで追加すると有用そうなツール案のメモ。

## 候補

1. 新着記事一覧（全体/PAK別）
2. 人気記事ランキング（閲覧数/ダウンロード数など）
3. タグ/カテゴリの集計（件数つき）
4. ユーザー別の公開記事一覧
5. 検索補助（タグ名/ユーザー名の前方一致サジェスト）

## 既存ロジックの共通化案

### 目的

- MCPツールとWebコントローラで重複している取得ロジックを一本化する。
- 返却形式の違い（View/JSON/MCP）だけを各層で扱う。

### 候補アプローチ

1. **サービスクラスに集約**
   - 例: `App\Services\Front\ArticleSearchService`
   - `getOptions()` / `search($condition, $limit)` / `show($userIdOrNickname, $slug)` を提供
   - WebはResource/Bladeに変換、MCPはResponse::jsonで返却

2. **既存Actionを拡張して流用**
   - `SearchAction` を options/search で利用し、MCP側はActionの返り値を整形して返却
   - `ShowController` 相当は新規 Action（例: `ShowAction`）を追加して両方から利用

### 追加で決めたい点

- 返却形の責務: Service/Actionは「モデル or Resource」までか、配列化まで行うか
- MCP用の「簡易オプション（id/nameのみ）」を共通化するか、MCP側で変換するか
- `limit` や `condition` のバリデーションはどこで統一するか

### 決定事項

- Actionにビジネスロジックを集約する
- レスポンス形式はツール/コントローラー側の責務とする
