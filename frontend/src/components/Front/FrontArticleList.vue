<template>
  <q-list>
    <q-item v-for="(a, i) in articles" :key="i" :to="{name:'show', params:{slug:a.slug}}">
      <q-item-section thumbnail>
        <img :src="thumbnailUrl(a)" />
      </q-item-section>

      <q-item-section>

        <q-item-label>{{a.title}}</q-item-label>
        <q-item-label caption lines="2">{{description(a)}}</q-item-label>
        <q-item-label>
          <category-list :article="a" />
          <tag-list :article="a" />
        </q-item-label>

      </q-item-section>
    </q-item>
  </q-list>
</template>
<script>
import { defineComponent } from 'vue';
import { render, sanitizeAll } from '../../composables/markdownParser';
import CategoryList from '../Common/CategoryList.vue';
import TagList from '../Common/TagList.vue';

const sectionTextableTypes = ['caption', 'text', 'url'];

const handlePage = (sections) => sections
  .filter((s) => sectionTextableTypes.includes(s.type))
  .map((s) => s[s.type])
  .join('');

const description = (article) => {
  switch (article.post_type) {
    case 'page':
      return handlePage(article.contents.sections);
    case 'markdown':
      return sanitizeAll(render(article.contents.markdown));
    default:
      return article.contents.description;
  }
};

const thumbnailUrl = (article) => {
  const attachmentId = parseInt(article.contents.thumbnail, 10);
  return article.attachments.find((a) => a.id === attachmentId)?.url
    || `${process.env.BACKEND_URL}/storage/default/image.png`;
};
export default defineComponent({
  name: 'FrontArticleList',
  props: {
    articles: {
      type: Array,
      default: () => [],
    },
  },
  setup() {
    return {
      thumbnailUrl,
      description,
    };
  },
  components: { CategoryList, TagList },
});
</script>
