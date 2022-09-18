<template>
  <article class="markdown">
    <content-title :article="article" />
    <content-thumbnail :article="article" :attachments="attachments" />

    <content-markdown :article="article" />
    <dl>
      <template v-if="article.categories.length">
        <dt>
          <text-sub-title text="カテゴリ" :preview="preview" />
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
    },
    preview: {
      type: Boolean,
      default: false
    }
  }
};
</script>
