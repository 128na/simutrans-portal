<template>
  <component :is="componentName" :article="article" />
</template>
<script>
import { defineComponent, computed } from 'vue';
import ArticleShowAddonIntroduction from '../Common/ArticleShowAddonIntroduction';
import ArticleShowAddonPost from '../Common/ArticleShowAddonPost';
import ArticleShowPage from '../Common/ArticleShowPage';
import ArticleShowMarkdown from '../Common/ArticleShowMarkdown';

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
          case 'addon-introduction':
            return ArticleShowAddonIntroduction;
          case 'addon-post':
            return ArticleShowAddonPost;
          case 'page':
            return ArticleShowPage;
          case 'markdown':
            return ArticleShowMarkdown;
          default:
            throw new Error(`invalid post type '${props.article.post_type}'`);
        }
      }),
    };
  },
});
</script>
