<template>
  <component :is="componentName" :article="article" />
</template>
<script>
import { defineComponent, computed } from 'vue';
import ArticleShowAddonIntroduction from 'src/components/Front/PostType/ArticleShowAddonIntroduction';
import ArticleShowAddonPost from 'src/components/Front/PostType/ArticleShowAddonPost';
import ArticleShowPage from 'src/components/Front/PostType/ArticleShowPage';
import ArticleShowMarkdown from 'src/components/Front/PostType/ArticleShowMarkdown';
import {
  POST_TYPE_ADDON_INTRODUCTION, POST_TYPE_ADDON_POST, POST_TYPE_PAGE, POST_TYPE_MARKDOWN,
} from 'src/const';

export default defineComponent({
  name: 'FrontArticleList',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    return {
      componentName: computed(() => {
        switch (props.article.post_type) {
          case POST_TYPE_ADDON_INTRODUCTION:
            return ArticleShowAddonIntroduction;
          case POST_TYPE_ADDON_POST:
            return ArticleShowAddonPost;
          case POST_TYPE_PAGE:
            return ArticleShowPage;
          case POST_TYPE_MARKDOWN:
            return ArticleShowMarkdown;
          default:
            throw new Error(`invalid post type '${props.article.post_type}'`);
        }
      }),
    };
  },
});
</script>
