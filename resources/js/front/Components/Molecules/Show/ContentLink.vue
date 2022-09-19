<template>
  <a :href="article.contents.link" :data-url="article.contents.link" :data-slug="article.slug" class="text-primary"
    target="_blank" rel="noopener noreferrer" @click="handle">{{ article.contents.link }}</a>
</template>
<script>
import axios from 'axios';

export default {
  props: {
    article: {
      type: Object,
      required: true
    }
  },
  methods: {
    handle() {
      axios.post(`/api/v1/click/${this.article.slug}`);
    }
  }
};
</script>
