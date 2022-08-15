<template>
  <component :is=component :article="article" />
</template>
<script>
import { POST_TYPES } from '../../../const';
export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  },
  computed: {
    component() {
      switch (this.article.post_type) {
        case POST_TYPES.ADDON_INTRODUCTION:
          return 'article-addon-introduction';
        case POST_TYPES.ADDON_POST:
          return 'article-addon-post';
        case POST_TYPES.PAGE:
          return 'article-page';
        case POST_TYPES.MARKDOWN:
          return 'article-markdown';
      }
      throw new Error(`invalid post type "${this.article.post_type}" provided.`);
    }
  }
};
</script>
