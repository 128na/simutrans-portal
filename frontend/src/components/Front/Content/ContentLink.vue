<template>
  <a :href="article.contents.link" class="text-primary" target="_blank" rel="noopener noreferrer" dusk="conversion-link"
    @click="handle">{{ article.contents.link }}</a>
</template>
<script>
import { useFrontApi } from 'src/composables/api';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'ContentLink',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const { postConversion } = useFrontApi();
    return {
      handle() {
        postConversion(props.article.slug);
      },
    };
  },
});
</script>
