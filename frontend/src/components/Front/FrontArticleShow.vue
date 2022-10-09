<template>
  <component :is="componentName" :article="article" />
</template>
<script>
import { defineComponent, computed } from 'vue';
import ArticleShowAddonIntroduction from 'src/components/Common/ArticleShowAddonIntroduction';
import ArticleShowAddonPost from 'src/components/Common/ArticleShowAddonPost';
import ArticleShowPage from 'src/components/Common/ArticleShowPage';
import ArticleShowMarkdown from 'src/components/Common/ArticleShowMarkdown';

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
