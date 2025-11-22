namespace TagEdit {
  type Tag = ArticleShow.TagShowable &
    SearchableOption & {
      id: number;
      name: string;
      description: string | null;
      editable: boolean;
      created_by: User | null;
      last_modified_by: User | null;
      last_modified_at: string | null;
      created_at: string;
      updated_at: string;
      articles_count: number;
    };
  type Creating = {
    id: null;
    name: null | string;
    description: null | string;
  };

  type User = {
    id: number;
    name: string;
  };
}
