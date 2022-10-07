<template>
  <div class="article-page">
    <section v-for="(section, index) in article.contents.sections" :key="index">
      <h2 v-if="isCaption(section)" class="my-2">{{ section.caption }}</h2>
      <text-pre v-if="isText(section)">{{section.text}}</text-pre>
      <div v-if="isUrl(section)" class="url">
        <a :href="section.url" class="text-primary" target="_blank" rel="noopener noreferrer">{{ section.url }}</a>
      </div>
      <div v-if="isImage(section)" class="imgage">
        <img class="img-fluid thumbnail shadow-sm" :src="imageUrl(section)">
      </div>
    </section>
  </div>
</template>
<script>
import { defineComponent } from 'vue';
import TextPre from './TextPre.vue';

export default defineComponent({
  name: 'ContentPage',
  props: {
    article: {
      type: Object,
      required: true,
    },
    attachments: {
      type: Array,
      default: () => [],
    },
  },
  setup(props) {
    return {
      isCaption(section) {
        return section.type === 'caption';
      },
      isText(section) {
        return section.type === 'text';
      },
      isUrl(section) {
        return section.type === 'url';
      },
      isImage(section) {
        return section.type === 'image';
      },
      imageUrl(section) {
        return props.attachments.find((i) => String(i.id) === String(section.id))?.url;
      },
    };
  },
  components: { TextPre },
});
</script>

<style lang="scss">
.article-page {
  img {
    max-width: 100%;
    filter: drop-shadow(0px 0px 2px $dark);
  }
}
</style>
