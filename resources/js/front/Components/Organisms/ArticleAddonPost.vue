<template>
<article class="addon-post">
  <content-title :article="article" />
  <content-thumbnail :article="article" />

  <dl>
    <dt><text-sub-title text="作者" /></dt>
    <dd><text-pre :text="article.contents.author"/></dd>
    <dt><text-sub-title text="投稿者" /></dt>
    <dd><link-internal :href="article.user.url"><text-pre :text="article.user.name"/></link-internal></dd>
    <template v-if="article.categories.length">
      <dt><text-sub-title text="カテゴリ" /></dt>
      <dd><content-categories :article="article" /></dd>
    </template>
    <template v-if="article.tags.length">
      <dt><text-sub-title text="タグ" /></dt>
      <dd><content-tags :article="article" /></dd>
    </template>
    <dt><text-sub-title text="説明" /></dt>
    <dd><text-pre :text="article.contents.description" /></dd>
    <template v-if="article.contents.thanks">
      <dt><text-sub-title text="謝辞・参考にしたアドオン" /></dt>
      <dd><text-pre :text="article.contents.thanks" /></dd>
    </template>
    <template v-if="article.contents.license">
      <dt><text-sub-title text="ライセンス" /></dt>
      <dd><text-pre :text="article.contents.license" /></dd>
    </template>
    <dt><text-sub-title text="添付ファイル情報（ベータ版機能）" /></dt>
    <dd><content-file-info :article="article" /></dd>
    <dt><text-sub-title text="ダウンロード" /></dt>
    <dd><content-download :article="article" /></dd>
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
