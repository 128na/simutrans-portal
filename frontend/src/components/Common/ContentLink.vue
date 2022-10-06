<template>
  <a :href="article.contents.link" class="text-primary" target="_blank" rel="noopener noreferrer" dusk="conversion-link"
    @click="handle">{{ article.contents.link }}</a>
</template>
<script>
import { defineComponent } from 'vue';
import { api } from '../../boot/axios';
import gtm from '../../boot/gtm';

export default defineComponent({
  name: 'ContentLink',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    return {
      handle() {
        api.post(`/api/v3/conversion/${props.article.slug}`);
        gtm.frontConversionLink();
      },
    };
  },
});
</script>
