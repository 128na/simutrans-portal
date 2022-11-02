<template>
  <span v-if="article.contents.agreement">この記事は作者の許可を得てまたは作者自身により掲載しています。</span>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'ContentAgreement',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
});
</script>
