<template>
  <div class="container-fluid py-4">
    <front-menu />
    <router-view :cachedArticles="cachedArticles" @addCache="handleAddCache" @addCaches="handleAddCaches" />
  </div>
</template>
<script>

export default {
  data() {
    return {
      cachedArticles: []
    };
  },
  methods: {
    handleAddCache(article) {
      const index = this.cachedArticles.findIndex(a => a.slug === article.slug);
      if (index === -1) {
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
