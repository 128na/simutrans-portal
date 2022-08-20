<template>
  <template-article :article="previewArticle" :preview="true" />
</template>
<script>
import { mapGetters } from 'vuex';
export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  },
  computed: {
    ...mapGetters([
      'user',
      'findAttachment',
      'getCategory'
    ]),
    previewArticle() {
      return {
        title: this.article.title || '  タイトルがありません',
        slug: this.article.slug,
        status: this.article.status,
        post_type: this.article.post_type,
        contents: this.article.contents,
        categories: this.article.categories,
        tags: this.article.tags,
        user: {
          name: this.user.name,
          url: this.user.url
        },
        url: `/articles/${this.article.slug}`,
        published_at: this.article.published_at ? this.article.published_at : '未投稿',
        modified_at: this.article.modified_at,
        images: [],
        file_info: {}
      };
    }
  },
  methods: {
    buildContents() { }
  }
};
</script>
