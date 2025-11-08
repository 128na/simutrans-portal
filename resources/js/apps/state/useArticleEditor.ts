import { create } from "zustand";
import { immer } from "zustand/middleware/immer";

type ArticleEditorState = {
  user: User.WithRole;
  article: Article.Editing;
  attachments: Attachment[];
  tags: Tag.Listing[];
  categories: Category.Grouping;
  relationalArticles: Article.Relational[];
  shouldNotfy: boolean;

  init: (initial: {
    user: User.WithRole;
    article: Article.Editing;
    attachments: Attachment[];
    tags: Tag.Listing[];
    categories: Category.Grouping;
    relationalArticles: Article.Relational[];
    shouldNotfy: boolean;
  }) => void;

  update: (fn: (draft: Article.Editing) => void) => void;
  updateContents: <T = Content>(fn: (draft: T) => void) => void;
  updateAttachments: (attachments: Attachment[]) => void;
  updateTags: (tags: Tag.Listing[]) => void;
  updateShouldNotify: (shouldNotify: boolean) => void;
};

export const useArticleEditor = create<ArticleEditorState>()(
  immer((set) => ({
    user: {} as User.WithRole,
    article: {} as Article.Editing,
    attachments: [],
    tags: [],
    categories: {} as Category.Grouping,
    relationalArticles: [],
    shouldNotfy: false,

    init(initial) {
      set({
        user: initial.user,
        article: structuredClone(initial.article),
        attachments: structuredClone(initial.attachments),
        tags: structuredClone(initial.tags),
        categories: initial.categories,
        relationalArticles: initial.relationalArticles,
        shouldNotfy: initial.shouldNotfy,
      });
    },

    update(fn) {
      set((state) => {
        if (state.article) fn(state.article);
      });
    },
    updateContents<T>(fn: (draft: T) => void) {
      set((state) => {
        if (state.article.contents) fn(state.article.contents as T);
      });
    },
    updateAttachments(attachments: Attachment[]) {
      set((state) => {
        state.attachments = attachments;
      });
    },
    updateTags(tags: Tag.Listing[]) {
      set((state) => {
        state.tags = tags;
      });
    },
    updateShouldNotify(shouldNotfy: boolean) {
      set((state) => {
        state.shouldNotfy = shouldNotfy;
      });
    },
  })),
);
