import { create } from "zustand";
import { immer } from "zustand/middleware/immer";

type ArticleEditorState = {
  initialArticle: ArticleEdit.Article;
  user: ArticleEdit.User;
  article: ArticleEdit.Article;
  attachments: AttachmentEdit.Attachment[];
  tags: TagEdit.Tag[];
  categories: Category.Grouping;
  relationalArticles: ArticleEdit.Relational[];
  /** 保存時にSNS通知するか */
  shouldNotify: boolean;
  /** 記事更新時に更新日を更新しないか */
  withoutUpdateModifiedAt: boolean;
  /** URL変更時にリダイレクト追加するか */
  followRedirect: boolean;

  init: (initial: {
    initialArticle: ArticleEdit.Article;
    user: ArticleEdit.User;
    article: ArticleEdit.Article;
    attachments: AttachmentEdit.Attachment[];
    tags: TagEdit.Tag[];
    categories: Category.Grouping;
    relationalArticles: ArticleEdit.Relational[];
    shouldNotify: boolean;
    withoutUpdateModifiedAt: boolean;
    followRedirect: boolean;
  }) => void;

  update: (fn: (draft: ArticleEdit.Article) => void) => void;
  updateContents: <T = Content>(fn: (draft: T) => void) => void;
  updateAttachments: (attachments: AttachmentEdit.Attachment[]) => void;
  updateTags: (tags: TagEdit.Tag[]) => void;
  updateShouldNotify: (shouldNotify: boolean) => void;
  updateWithoutUpdateModifiedAt: (withoutUpdateModifiedAt: boolean) => void;
  updateFollowRedirect: (followRedirect: boolean) => void;
};

export const useArticleEditor = create<ArticleEditorState>()(
  immer((set) => ({
    initialArticle: {} as ArticleEdit.Article,
    user: {} as ArticleEdit.User,
    article: {} as ArticleEdit.Article,
    attachments: [],
    tags: [],
    categories: {} as Category.Grouping,
    relationalArticles: [],
    shouldNotify: false,
    withoutUpdateModifiedAt: false,
    followRedirect: false,

    init(initial) {
      set({
        initialArticle: initial.article,
        user: initial.user,
        article: structuredClone(initial.article),
        attachments: structuredClone(initial.attachments),
        tags: structuredClone(initial.tags),
        categories: initial.categories,
        relationalArticles: initial.relationalArticles,
        shouldNotify: initial.shouldNotify,
        withoutUpdateModifiedAt: initial.withoutUpdateModifiedAt,
        followRedirect: initial.followRedirect,
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
    updateWithoutUpdateModifiedAt(withoutUpdateModifiedAt: boolean) {
      set((state) => {
        state.withoutUpdateModifiedAt = withoutUpdateModifiedAt;
      });
    },
    updateFollowRedirect(followRedirect: boolean) {
      set((state) => {
        state.followRedirect = followRedirect;
      });
    },
  })),
);

export const useIsArticleUpdated = () =>
  useArticleEditor(
    (state) =>
      JSON.stringify(state.initialArticle) !== JSON.stringify(state.article),
  );

export const useIsSlugUpdated = () =>
  useArticleEditor(
    (state) =>
      state.initialArticle.slug &&
      state.initialArticle.slug !== state.article.slug,
  );
