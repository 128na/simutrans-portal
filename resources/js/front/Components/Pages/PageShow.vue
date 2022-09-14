<template>
  <template-article v-if="article" :article="article" :attachments="article.attachments" />
  <div v-else>Loading</div>
</template>
<script>
import axios from 'axios';

export default {
  props: ['cachedArticles'],
  created() {
    if (!this.article) {
      this.fetchArticle(this.$route.params.slug);
    }
  },
  computed: {
    article() {
      const slug = encodeURI(this.$route.params.slug);
      return this.cachedArticles.find(a => a.slug === slug);
    }
  },
  methods: {
    async fetchArticle(slug) {
      const res = await axios.get(`/api/v3/front/articles/${slug}`);

      if (res.status === 200) {
        this.$emit('addCache', res.data.data);
      }
    }
  }
};
</script>
