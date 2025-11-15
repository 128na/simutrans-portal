import { create } from "zustand";
import { immer } from "zustand/middleware/immer";

type ArticleEditorState = {
  user: ArticleEdit.User;
  article: ArticleEdit.Article;
  attachments: AttachmentEdit.Attachment[];
  tags: TagEdit.Tag[];
  categories: Category.Grouping;
  relationalArticles: ArticleEdit.Relational[];
  shouldNotify: boolean;
  withoutUpdateModifiedAt: boolean;

  init: (initial: {
    user: ArticleEdit.User;
    article: ArticleEdit.Article;
    attachments: AttachmentEdit.Attachment[];
    tags: TagEdit.Tag[];
    categories: Category.Grouping;
    relationalArticles: ArticleEdit.Relational[];
    shouldNotify: boolean;
    withoutUpdateModifiedAt: boolean;
  }) => void;

  update: (fn: (draft: ArticleEdit.Article) => void) => void;
  updateContents: <T = Content>(fn: (draft: T) => void) => void;
  updateAttachments: (attachments: AttachmentEdit.Attachment[]) => void;
  updateTags: (tags: TagEdit.Tag[]) => void;
  updateShouldNotify: (shouldNotify: boolean) => void;
};

export const useArticleEditor = create<ArticleEditorState>()(
  immer((set) => ({
    user: {} as ArticleEdit.User,
    article: {} as ArticleEdit.Article,
    attachments: [],
    tags: [],
    categories: {} as Category.Grouping,
    relationalArticles: [],
    shouldNotify: false,
    withoutUpdateModifiedAt: false,

    init(initial) {
      set({
        user: initial.user,
        article: structuredClone(initial.article),
        attachments: structuredClone(initial.attachments),
        tags: structuredClone(initial.tags),
        categories: initial.categories,
        relationalArticles: initial.relationalArticles,
        shouldNotify: initial.shouldNotify,
        withoutUpdateModifiedAt: initial.withoutUpdateModifiedAt,
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
    updateAttachments(attachments: AttachmentEdit.Attachment[]) {
      set((state) => {
        state.attachments = attachments;
      });
    },
    updateTags(tags: TagEdit.Tag[]) {
      set((state) => {
        state.tags = tags;
      });
    },
    updateShouldNotify(shouldNotify: boolean) {
      set((state) => {
        state.shouldNotify = shouldNotify;
      });
    },
  })),
);
