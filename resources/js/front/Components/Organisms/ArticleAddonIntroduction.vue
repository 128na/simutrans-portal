<template>
<article class="addon-introduction">
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
    <template v-if="article.contents.agreement">
    <dt><text-sub-title text="掲載許可" /></dt>
    <dd><content-agreement :article="article" /></dd>
    </template>
    <dt><text-sub-title text="掲載先URL" /></dt>
    <dd><content-link :article="article" /></dd>
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
