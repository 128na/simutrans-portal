# API

新着一覧取得など一部のAPIを公開しています。

## 制限事項
APIは単一IPあたり100回/分の制限があります。
制限を超過すると429レスポンスが返されるようになります。
制限は1分経過すると解除されます。



# v1 API
システム内で使用しているのみで非公開です。

# v2 API
一般公開されています。

## API一覧
以下のAPIが利用可能です。

|メソッド|URL|説明|
|---|---|---|
| GET | https://simutrans.sakura.ne.jp/portal/api/v2/articles/latest | 新着アドオン記事一覧を返します | 
| GET | https://simutrans.sakura.ne.jp/portal/api/v2/articles/search?word={your_keyword} | 指定キーワードに該当する新着記事一覧を返します | 
| GET | https://simutrans.sakura.ne.jp/portal/api/v2/articles/user/{user_id} | 指定ユーザーの新着記事一覧を返します | 
| GET | https://simutrans.sakura.ne.jp/portal/api/v2/articles/category/{category_id} | 指定カテゴリを持つ新着記事一覧を返します | 
| GET | https://simutrans.sakura.ne.jp/portal/api/v2/articles/tag/{tag_id} | 指定タグを持つ新着記事一覧を返します | 


## レスポンス

### レスポンスステータスコード

|ステータスコード|状態|
|---|---|
|200|リクエストが成功しました。いい感じのレスポンスです。|
|404|リクエスト対象が見つかりません。Never give up!|
|422|リクエストのパラメーターに問題があります。超がんばれ|
|429|リクエスト頻度が単一IPあたり60回/分の制限に達しました。ゆっくりしていってね。|
|その他400系|リクエストに何らかの問題があります。諦めたらそこで試合終了ですよ。|
|その他500系|システム内のエラーです。中の人が気付いたら直します。|

### レスポンスボディ
全てのAPIが同様の構造の記事一覧を返します。
一覧は20件ごとに分かれており20件目以降の記事は `data.links.next` のURLから取得できます。

```
{
  "data": [
    {
      "id": int 記事ID,
      "title": string 記事タイトル,
      "post_type": string 記事形式(addon-introduction|addon-post|page),
      "contents": string 記事本文の一部,
      "url": string 記事URL,
      "author": string|null アドオン作者,
      "categories": [
        {
          "type": string カテゴリタイプ名,
          "slug": string カテゴリスラッグ,
          "url": string カテゴリ記事一覧のURL,
          "api": string カテゴリ記事一覧APIのURL
        },
        ...
      ],
      "created_by": {
        "name": string 記事制作ユーザー名,
        "url": string ユーザー記事一覧のURL,
        "api": string ユーザー記事一覧APIのURL
      },
    },
    ...
  ],
  "links": {
    "first": string 一覧最初ページのURL,
    "last": string 一覧最終ページのURL,
    "prev": string 一覧一つ前ページのURL,
    "next": string 一覧一つ次ページのURL
  },
  "meta": {
    "current_page": int 現在のページ番号,
    "last_page": int 最後のページ番号,
    "per_page": int 1ページ当たりの記事数,
    "from": int 返却した一覧の最初の記事番号,
    "to": int 返却した一覧の最後の記事番号,
    "total": int 記事の総数
    "path": string リクエストしたAPI URL,
  }
}
