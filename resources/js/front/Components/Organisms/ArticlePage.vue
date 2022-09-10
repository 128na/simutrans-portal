<template>
  <article class="page">
    <content-title :article="article" />
    <content-thumbnail :article="article" :attachments="attachments" />

    <content-page :article="article" :attachments="attachments" />
    <dl>
      <template v-if="article.categories.length">
        <dt>
          <text-sub-title text="カテゴリ" />
        </dt>
        <dd>
          <content-categories :article="article" />
        </dd>
      </template>
    </dl>
    <content-meta :article="article" />
  </article>
</template>
<script>
export default {
  props: {
    article: {
      type: Object,
      required: true
    },
    attachments: {
      type: Array,
      default: () => []
    }
  }
};
</script>
