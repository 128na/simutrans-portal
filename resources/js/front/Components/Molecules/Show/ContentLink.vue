<template>
  <a :href="article.contents.link" class="text-primary" target="_blank" rel="noopener noreferrer" dusk="conversion-link"
    @click="handle">{{ article.contents.link }}</a>
</template>
<script>
import axios from 'axios';
import gtm from '../../../../gtm';

export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  },
  methods: {
    handle() {
      axios.post(`/api/v3/conversion/${this.article.slug}`);
      gtm.frontConversionLink();
    }
  }
};
</script>
