<template>
  <template-article v-if="article" :article="article" :attachments="attachments" />
  <div v-else>Loading</div>
</template>
<script>
import axios from 'axios';

export default {
  data() {
    return {
      article: null,
      attachments: null
    };
  },
  created() {
    console.log(this.$route.params.slug);
    this.fetchArticle(this.$route.params.slug);
  },
  methods: {
    async fetchArticle(slug) {
      const res = await axios.get(`/api/v3/front/articles/${slug}`);
      console.log(res.data);

      if (res.status === 200) {
        this.article = res.data.data;
        this.attachments = res.data.data.attachments;
      }
    }
  }
};
</script>
