<template>
  <div class="container-fluid py-4">
    <front-menu :pak_addon_counts="pak_addon_counts" :user_addon_counts="user_addon_counts" />
    <router-view :cachedArticles="cachedArticles" @addCache="handleAddCache" @addCaches="handleAddCaches" />
  </div>
</template>
<script>
import axios from 'axios';

export default {
  data() {
    return {
      pak_addon_counts: null,
      user_addon_counts: null,
      cachedArticles: []
    };
  },

  mounted() {
    if (!this.sidebar) {
      this.fetchSidebar();
    }
  },
  methods: {
    async fetchSidebar() {
      const res = await axios.get('/api/v3/front/sidebar');
      if (res.status === 200) {
        this.pak_addon_counts = res.data.pak_addon_counts;
        this.user_addon_counts = res.data.user_addon_counts;
      }
    },
    handleAddCache(article) {
      const index = this.cachedArticles.findIndex(a => a.slug === article.slug);
      if (!index === -1) {
        this.cachedArticles.push(article);
      } else {
        this.cachedArticles.splice(index, 1, article);
      }
    },
    handleAddCaches(articles) {
      articles.map(a => this.handleAddCache(a));
    }
  }
};
</script>
