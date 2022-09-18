<template>
  <section class="mb-4 list">
    <h2 class="section-title">{{ title }}</h2>
    <mode-switcher v-model="mode" />
    <list-articles v-if="mode=='list'" :articles="articles" />
    <template v-else>
      <template-article v-for="article in articles" :key="article.slug" :article="article"
        :attachments="article.attachments" class="mb-4" />
    </template>
  </section>
</template>
<script>
import axios from 'axios';
import { watchAndFetch } from '../../mixins';

export default {
  props: ['cachedArticles'],
  mixins: [watchAndFetch],
  data() {
    return {
      articles: [],
      pagination: [],
      mode: 'list'
    };
  },
  computed: {
    title() {
      switch (this.$route.name) {
        // case 'user':
        // return `${this.$route.params.id}`;
        default:
          return '記事一覧';
      }
    }
  },
  methods: {
    fetch() {
      switch (this.$route.name) {
        case 'user':
          return this.fetchByUser(this.$route.params.id);
      }
    },
    async fetchByUser(userId) {
      try {
        const res = await axios.get(`/api/v3/front/user/${userId}`);
        this.handleResponse(res);
      } catch (err) {
        this.handleError(err);
      }
    },
    handleResponse(res) {
      if (res.status === 200) {
        this.articles = res.data.data;
        this.pagination = res.data.meta.links;
        this.$emit('addCaches', res.data.data);
      }
    }
  }
};
</script>
