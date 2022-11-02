<template>
  <div>
    <div v-html="sanitized" class="article-markdown" />
  </div>
</template>
<script>
import { defineComponent, computed } from 'vue';
import { useMarkdown } from 'src/composables/markdown';

export default defineComponent({
  name: 'ContentMarkdown',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const { render, sanitize } = useMarkdown();
    return {
      sanitized: computed(() => sanitize(render(props.article.contents.markdown))),
    };
  },
});
</script>
<style lang="scss">
.article-markdown {
  img {
    max-width: 100%;
    filter: drop-shadow(0px 0px 2px $dark);
  }
}
</style>
