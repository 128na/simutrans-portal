namespace ArticleList {
  type Article = {
    id: number | null;
    title: string;
    url: string;
    thumbnail: string;
    description: string;
    user: ArticleShow.User;
    categories: Category.Search[];
    tags: Tag[];
    published_at: string | null;
    modified_at: string | null;
  };
}
