<template>
  <main id="article-show">
    <message-loading v-show="loading" />
    <template-article v-if="article" :article="article" :attachments="article.attachments" />
  </main>
</template>
<script>
import axios from 'axios';
import { watchAndFetch, titleResolver } from '../../mixins';
export default {
  props: ['cachedArticles'],
  mixins: [watchAndFetch, titleResolver],
  data() {
    return {
      loading: true
    };
  },
  computed: {
    article() {
      return this.cachedArticles.find(a => a.slug === this.$route.params.slug);
    }
  },
  methods: {
    async fetch() {
      axios.post(`/api/v3/shown/${this.$route.params.slug}`);
      if (this.cachedArticles.find(a => a.slug === this.$route.params.slug)) {
        this.loading = false;
        this.title = this.article.title;
        return;
      }

      this.loading = true;
      try {
        const res = await axios.get(`/api/v3/front/articles/${this.$route.params.slug}`);
        if (res.status === 200) {
          this.$emit('addCache', res.data.data);
        }
      } catch (err) {
        this.handleError(err);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
