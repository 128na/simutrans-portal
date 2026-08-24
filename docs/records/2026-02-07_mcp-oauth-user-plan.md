# MCP ログインユーザー向け（OAuth対応） 実装計画

## 目的

- ログインユーザー向けのMCPサーバーを構築する。
- OAuth機能を用意し、MCPクライアントから安全に認可されたAPIアクセスを行う。
- MCPツールは当面「自身の記事一覧」を取得するツールのみを提供する。

## 想定範囲（初期版）

- MCPエンドポイント: 既存の /mcp と分離して /mcp-auth など専用のエンドポイントを新設
- MCPサーバー: User向け専用サーバーを新設（Guestとは別クラス）
- 認可: OAuth 2.0 Authorization Code (PKCE) を採用（ChatGPT連携を想定）
- ツール: "my-articles-tool" (ログインユーザーの公開/下書き含む自身の記事一覧)

## 既存構成の確認ポイント

- 認証: Laravel Fortify が導入済み
- 認証: Laravel Fortify が導入済み
- 認可: Laravel Passport を利用（OAuth2）
- 認証済みユーザーの取得方法: MCPリクエストとセッション/トークンの結合方法
- 既存のRepository/Action: 自身の記事一覧に対応する既存ロジックの再利用可否
- 既存OAuth系URL: /admin/oauth/twitter/authorize, /admin/oauth/twitter/callback

## 実装方針

### 1. MCPサーバー（ログインユーザー用）

- 新規クラスを追加
  - 例: App\Mcp\Servers\SimutransAddonPortalUserServer
- ルーティングを分離（例: /mcp-auth）
- 認証ミドルウェア or MCPの認可フローでユーザーを特定

### 2. 認可フロー（OAuth2 + PKCE）

- Authorization Code + PKCE を基本方針とする
- MCP用の認可/トークン発行エンドポイントを用意
- MCPクライアント側からはアクセストークンをAuthorizationヘッダで送信
- MCP側はトークン検証しユーザーを確定

### 3. ツール: 自身の記事一覧

- Tool名案: user-my-articles-tool
- 入力: limit, page (必要なら)
- 出力: ArticleList 相当を再利用（公開/下書きを含める）

### 4. テスト

- Feature: MCPツールのレスポンスに email / password が含まれないことを検証
- 認可: 不正/未認可トークン時に error を返すことを検証

## 実装タスク

1. 既存OAuth実装の確認（使用ライブラリ・ルート・スコープ）
2. User向けMCPサーバー/ルート追加
3. 認証フロー統合（Bearerトークンの検証）
4. User向けツール（自身の記事一覧）の実装
5. テスト追加
6. セキュリティレビュー（漏洩フィールド確認）

## 確認したい点

- OAuthクライアントは管理画面で登録する
- 認可スコープは read/write の2種（writeは当面未使用）

## 次のアクション

- OAuthの現状構成を共有してもらい、詳細設計を確定
- MCPユーザー向けツールの仕様確定
