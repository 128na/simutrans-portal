export const analyticsFilter = (
  articles: Analytics.Article[],
  criteria: string,
  selected: number[]
) => {
  const q = criteria.trim().toLowerCase();
  if (!q) return articles;

  return articles.filter((t) => {
    if (selected.includes(t.id)) {
      return true;
    }
    return t.title.toLowerCase().includes(q);
  });
};
