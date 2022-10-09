<template>
  <template v-if="listMode==='list'">
    <q-list>
      <q-item v-for="(a, i) in articles" :key="i" :to="{name:'show', params:{slug:a.slug}}" tag="article">
        <q-item-section side>
          <q-img :src="thumbnailUrl(a)" fit="contain" width="240px" height="135px" class="bg-grey-1 gt-sm" />
          <q-img :src="thumbnailUrl(a)" fit="contain" width="160px" height="90px" class="bg-grey-1 lt-md" />
        </q-item-section>
        <q-item-section top>
          <q-item-label class="text-h4">
            {{a.title}}
          </q-item-label>
          <q-item-label caption lines="2">{{description(a)}}</q-item-label>
          <q-item-label>
            <category-list :article="a" />
            <tag-list :article="a" />
          </q-item-label>
          <q-item-label caption>
            <router-link class="default-link" :to="{name:'user', params:{id:a.user.id}}">
              {{ a.user.name }}
            </router-link>
          </q-item-label>
          <q-item-label caption>
            <content-meta :article="a" />
          </q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </template>
  <template v-else-if="listMode==='gallery'">
    <div class="q-col-gutter-md row items-start">
      <article v-for="(a, i) in articles" :key="i" class="col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
        <router-link :to="{name:'show', params:{slug:a.slug}}">
          <q-img :to="{name:'show', params:{slug:a.slug}}" :src="thumbnailUrl(a)" width="100%" ratio="1" fit="contain"
            class="bg-grey-1">
            <div class="text-h5 absolute-bottom text-center">
              {{ a.title }}
            </div>
          </q-img>
        </router-link>
      </article>
    </div>
  </template>
  <template v-else>
    <q-list separator>
      <template v-for="(a, i) in articles" :key="i">
        <q-item>
          <front-article-show :article="a" />
        </q-item>
      </template>
    </q-list>
  </template>
</template>
<script>
import { defineComponent } from 'vue';
import { render, sanitizeAll } from '../../composables/markdownParser';
import CategoryList from '../Common/CategoryList.vue';
import TagList from '../Common/TagList.vue';
import ContentMeta from '../Common/ContentMeta.vue';
import FrontArticleShow from './FrontArticleShow.vue';

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
    listMode: {
      type: String,
      default: 'list',
    },
  },
  setup() {
    return {
      thumbnailUrl,
      description,
    };
  },
  components: {
    CategoryList, TagList, ContentMeta, FrontArticleShow,
  },
});
</script>
<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity .1s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: .2;
}
</style>
