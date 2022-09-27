<template>
  <main>
    <section class="mb-5" v-for="c in contents" :key="c.label">
      <router-link :to="c.to">
        <h2 class="section-title">{{c.label}}</h2>
      </router-link>
      <message-loading v-show="c.loading" />
      <message-error v-show="c.error" @reload="fetchContent(c)" />
      <list-articles :articles="c.articles" />
    </section>
  </main>
</template>
<script>
import axios from 'axios';
import { watchAndFetch, titleResolver } from '../../mixins';
export default {
  mixins: [watchAndFetch, titleResolver],
  data() {
    return {
      contents: [
        {
          api: '/api/v3/front/category/pak/128-japan?page=1',
          to: { name: 'category', params: { type: 'pak', slug: '128-japan' } },
          label: 'pak128Japanの新着アドオン',
          articles: [],
          error: false,
          loading: true
        },
        {
          api: '/api/v3/front/category/pak/128?page=1',
          to: { name: 'category', params: { type: 'pak', slug: '128' } },
          label: 'pak128の新着アドオン',
          articles: [],
          error: false,
          loading: true
        },
        {
          api: '/api/v3/front/category/pak/64?page=1',
          to: { name: 'category', params: { type: 'pak', slug: '64' } },
          label: 'pak64の新着アドオン',
          articles: [],
          error: false,
          loading: true
        },
        {
          api: '/api/v3/front/ranking?page=1',
          to: { name: 'ranking' },
          label: 'アクセスランキング',
          articles: [],
          error: false,
          loading: true
        },
        {
          api: '/api/v3/front/pages?page=1',
          to: { name: 'pages' },
          label: '一般記事',
          articles: [],
          error: false,
          loading: true
        },
        {
          api: '/api/v3/front/announces?page=1',
          to: { name: 'announces' },
          label: 'お知らせ',
          articles: [],
          error: false,
          loading: true
        }
      ]
    };
  },
  methods: {
    fetch() {
      this.contents.map(c => this.fetchContent(c));
      this.title = 'Top';
    },
    async fetchContent(content) {
      content.loading = true;
      content.error = false;
      content.articles = [];
      try {
        const res = await axios.get(content.api);
        if (res.status === 200) {
          this.$emit('addCaches', res.data.data);
          content.articles = JSON.parse(JSON.stringify(res.data.data)).splice(0, 3);
        }
      } catch (err) {
        content.error = true;
        console.warn(err.response);
      } finally {
        content.loading = false;
      }
    }

  }
};
</script>
