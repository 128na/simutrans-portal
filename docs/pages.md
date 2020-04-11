# ページ構成

| パス | ログイン | 説明 | パラメーター |
| --- | :---: | --- | --- |
| / | 不要 | トップページ | なし |
| /addons  | 不要 |  アドオン記事一覧  | なし |
| /ranking  | 不要 |  デイリーPVランキング一覧  | なし |
| /category/{type}/{name}  | 不要 |  カテゴリの記事一覧  | カテゴリタイプ名、カテゴリスラッグ名 |
| /category/pak/{pak}/{addon}  | 不要 |  pak別アドオンタイプ別の記事一覧  | pak名、アドオンタイプ名 |
| /tag/{tag}  | 不要 |  タグ別記事一覧 | タグID |
| /user/{user}  | 不要 |  ユーザー別記事一覧 | ユーザーID |
| /search?word={word}  | 不要 |  記事検索結果一覧 | キーワード |
| /mypage  | 不要 |  マイページSP表示画面 | なし |


# MypageSPAページ構成

| パス | ログイン | メール認証 | 説明 | パラメーター |
| --- | :---: | :---: | --- | --- |
| /mypage/register | 不要 | 不要 | 新規登録 | なし |
| /mypage/reset | 不要 | 不要 | PWリセットメール送信 | なし |
| /mypage/login | 不要 | 不要 | ログイン | なし |
| /mypage | 必要 | 不要 | マイページトップ | なし |
| /mypage/create/{post_type} | 必要 | 必要 | 記事作成 | 記事形式 |
| /mypage/edit/{article} | 必要 | 必要 | 記事編集 | 記事ID |
| /mypage/analytics/ | 必要 | 必要 | アクセス解析 | なし |
| /mypage/profile | 必要 | 必要 | プロフィール編集 | なし |
