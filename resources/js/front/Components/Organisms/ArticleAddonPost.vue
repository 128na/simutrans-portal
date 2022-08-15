<template>
<article class="addon-post">
  <content-title :article="article" />
  <content-thumbnail :article="article" />

  <dl>
    <dt><text-sub-title text="作者 / 投稿者" /></dt>
    <dd><text-pre :text="article.contents.author"/></dd>
    <dt><text-sub-title text="カテゴリ" /></dt>
    <dd></dd>
    <dt><text-sub-title text="タグ" /></dt>
    <dd></dd>
    <dt><text-sub-title text="説明" /></dt>
    <dd><text-pre :text="article.contents.description" /></dd>
    <dt><text-sub-title text="謝辞・参考にしたアドオン" /></dt>
    <dd><text-pre :text="article.contents.thanks" /></dd>
    <dt><text-sub-title text="ライセンス" /></dt>
    <dd><text-pre :text="article.contents.license" /></dd>
    <dt><text-sub-title text="添付ファイル情報（ベータ版機能）" /></dt>
    <dd><content-file-info :article="article" /></dd>
    <dt><text-sub-title text="ダウンロード" /></dt>
    <dd><content-download :article="article" /></dd>
    <dd></dd>
  </dl>
</article>
</template>
<script>
export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  }
};
</script>
