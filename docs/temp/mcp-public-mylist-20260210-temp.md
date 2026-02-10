# MCP public mylist tools

キーワード: mcp,mylist,public,tools,api,response,laravel
最終更新日：2026-02-10
ステータス：完了

## 概要

公開マイリストの一覧取得・詳細取得をMCPツールとして追加する。既存の公開マイリストAPI/サービスとロジックを共通化し、MCPレスポンスの項目説明をdescriptionに記載する。

## 対象範囲

- 追加対象: 公開マイリスト一覧/詳細のMCPツール
- 既存ロジックの共通化: PublicMyListController と新MCPツールで共通Actionを利用
- 既存APIの挙動変更は最小限

## 想定レスポンス（案）

### 公開マイリスト詳細

- 既存の公開APIと同じ形に合わせる
- 形式:
  - data: MyListItem の配列
  - list: MyListShow (id, title, note, is_public, slug, items_count, created_at, updated_at)

### 公開マイリスト一覧

- data: 公開MyListの配列
  - id, title, note, is_public, slug, items_count, created_at, updated_at
- links / meta: ページネーション情報

## 実装案

1. `app/Actions/FrontMyList` を追加し、公開マイリスト取得のActionを作成
   - 公開詳細: slug -> list + items (paginator)
   - 公開一覧: public list paginator
2. `PublicMyListController@showPublic` でActionを利用
3. MCPツールを追加
   - 公開一覧ツール
   - 公開詳細ツール
4. MCPサーバーにツール登録
5. description にレスポンス項目の説明を追記

## 決定事項

- 公開一覧ツールのフィルタは付けない（全件）
- 公開一覧/詳細レスポンスに `user` 情報は含めない
