<template>
  <section class="mb-4 list">
    <h2 class="section-title">{{ title }}</h2>
    <mode-switcher v-model="mode" />
    <list-paginator :pagination="pagination" />
    <message-loading v-show="loading" />
    <message-error v-show="error" @reload="fetch" />
    <list-articles v-if="mode=='list'" :articles="articles" />
    <template v-else>
      <template-article v-for="article in articles" :key="article.slug" :article="article"
        :attachments="article.attachments" class="mb-4" />
    </template>
    <list-paginator :pagination="pagination" />
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
      loading: true,
      error: false,
      articles: [],
      pagination: null,
      mode: 'list'
    };
  },
  computed: {
    title() {
      switch (this.$route.name) {
        case 'search':
          return `「${this.$route.query.word}」の検索結果`;
        default:
          return '記事一覧';
      }
    }
  },
  methods: {
    async fetch() {
      this.loading = true;
      this.error = false;
      this.articles = [];
      try {
        const res = await (async () => {
          switch (this.$route.name) {
            case 'categoryPak':
              return this.fetchCategoryPak();
            case 'category':
              return this.fetchCategory();
            case 'tag':
              return this.fetchTag();
            case 'user':
              return this.fetchUser();
            case 'announces':
              return this.fetchAnnounces();
            case 'pages':
              return this.fetchPages();
            case 'ranking':
              return this.fetchRanking();
            case 'search':
              return this.fetchSearch();
            default:
              throw new Error(`unknown route name "${this.$route.name}" provided"`);
          }
        })();
        this.handleResponse(res);
      } catch (err) {
        this.error = true;
        console.warn(err.response);
      } finally {
        this.loading = false;
      }
    },
    fetchUser() {
      return axios.get(`/api/v3/front/user/${this.$route.params.id}?page=${this.$route.query.page || 1}`);
    },
    fetchCategoryPak() {
      return axios.get(`/api/v3/front/category/pak/${this.$route.params.size}/${this.$route.params.slug}?page=${this.$route.query.page || 1}`);
    },
    fetchCategory() {
      return axios.get(`/api/v3/front/category/${this.$route.params.type}/${this.$route.params.slug}?page=${this.$route.query.page || 1}`);
    },
    fetchTag() {
      return axios.get(`/api/v3/front/tag/${this.$route.params.id}?page=${this.$route.query.page || 1}`);
    },
    fetchAnnounces() {
      return axios.get(`/api/v3/front/announces?page=${this.$route.query.page || 1}`);
    },
    fetchPages() {
      return axios.get(`/api/v3/front/pages?page=${this.$route.query.page || 1}`);
    },
    fetchRanking() {
      return axios.get(`/api/v3/front/ranking?page=${this.$route.query.page || 1}`);
    },
    fetchSearch() {
      return axios.get(`/api/v3/front/search?word=${this.$route.query.word}&page=${this.$route.query.page || 1}`);
    },
    handleResponse(res) {
      if (res.status === 200) {
        this.articles = res.data.data;
        this.pagination = res.data.meta;
        this.$emit('addCaches', res.data.data);
      }
    }
  }
};
</script>
