<template>
  <div>
    <section v-for="(section, index) in article.contents.sections" :key="index">
      <h2 v-if="isCaption(section)" class="my-2">{{ section.caption }}</h2>
      <text-pre v-if="isText(section)" :text="section.text" />
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
export default {
  props: {
    article: {
      type: Object,
      required: true
    },
    attachments: {
      type: Array,
      default: () => []
    }
  },
  methods: {
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
      return this.attachments.find(i => i.id == section.id)?.url;
    }
  }
};
</script>
