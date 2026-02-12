# 公開マイリスト一覧ページ

キーワード: 公開マイリスト, 一覧, フロント, MCP, ページ, ルーティング, API
最終更新日：2026-02-12
ステータス：完了

## 概要

公開マイリストの一覧ページを新設する。取得処理は既存のMCPツールが利用しているPublicListActionを共通化して使い、フロントはReactの一覧描画コンポーネントで表示する。必要に応じてページネーションとソートを追加し、既存の公開マイリスト詳細ページへの導線を用意する。

## 対応方針

- ルート: /mylist を一覧ページに割り当て、/mylist/{slug} の詳細と共存させる。
- 取得API: /api/v1/mylist/public を追加し、PublicListActionを利用して一覧を返す。
- 画面: resources/views/pages/mylist/index.blade.php を追加し、Reactで一覧を描画する。
- 共通化: PublicListActionを新APIとMCPツールの両方で利用する。

## 作業項目

- ルーティング追加（web.php, api.php）
- PublicMyListControllerに一覧表示/取得メソッド追加
- 公開マイリスト一覧ページ（Blade + React）追加
- フロント一覧UIとページネーション実装
- サイドバー等に導線が必要なら追記

## 確認事項

- 一覧ページのURL（デフォルト: /mylist）->それでOK
- 一覧のソート（デフォルト: updated_at:desc）->それでOK
- ページネーションを使うか（デフォルト: 1ページ20件）->それでOK
- サイドバーやトップからの導線追加の有無->サイドバーのその他の中に追加
