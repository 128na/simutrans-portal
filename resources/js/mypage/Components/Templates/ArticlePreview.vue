<template>
  <div>
    <b-alert show variant="warning">プレビュー表示</b-alert>
    <template-article :article="previewArticle" :attachments="attachments" :preview="true" />
  </div>
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
      'attachments',
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
          id: this.user.id,
          name: this.user.name
        },
        url: `/articles/${this.article.slug}`,
        published_at: this.article.published_at ? this.article.published_at : '未投稿',
        modified_at: this.article.modified_at,
        file_info: {}
      };
    }
  },
  methods: {
    buildContents() { }
  }
};
</script>
