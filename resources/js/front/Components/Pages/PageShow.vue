<template>
  <main id="article-show">
    <template-article v-if="article" :article="article" :attachments="article.attachments" />
    <div v-else>Loading</div>
  </main>
</template>
<script>
import axios from 'axios';
import { watchAndFetch } from '../../mixins';
export default {
  props: ['cachedArticles'],
  mixins: [watchAndFetch],
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
      if (this.cachedArticles.find(a => a.slug === this.$route.params.slug)) {
        this.loading = false;
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
