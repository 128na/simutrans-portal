<template>
  <div>
    <b-alert show variant="warning">プレビュー表示です。一部のリンクは無効化されています。</b-alert>
    <template-article :article="previewArticle" :attachments="attachments" :preview="true" />
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
import { DateTime } from 'luxon';
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
        published_at: this.article.published_at
          ? DateTime.fromISO(this.article.published_at).toFormat('yyyy-LL-dd HH:mm:ss')
          : '未投稿',
        modified_at: DateTime.fromISO(this.article.modified_at).toFormat('yyyy-LL-dd HH:mm:ss'),
        file_info: {}
      };
    }
  },
  methods: {
    buildContents() { }
  }
};
</script>
