<template>
  <dt>関連記事</dt>
  <dd>
    <template v-for="a in articles" :key="a.id">
      <div>
        <a :href="`/articles/${a.id}`" target="_blank" rel="noreferrer noopener">{{ a.title }}</a>
      </div>
    </template>
  </dd>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'RelatedArticles',
  components: {
  },
  props: {
    articles: {
      type: Array,
      required: true,
    },
  },
  setup() {
  },
});
</script>
