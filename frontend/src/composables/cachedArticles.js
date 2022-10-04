import { reactive } from 'vue';

export const cachedArticles = () => {
  const state = reactive({ cachedArticles: [] });
  const handleAddCache = (article) => {
    const index = state.cachedArticles.findIndex((a) => a.slug === article.slug);
    if (index === -1) {
      state.cachedArticles.push(article);
    } else {
      state.cachedArticles.splice(index, 1, article);
    }
  };
  const handleAddCaches = (articles) => {
    articles.map((a) => handleAddCache(a));
  };

  return {
    state,
    handleAddCache,
    handleAddCaches,
  };
};
