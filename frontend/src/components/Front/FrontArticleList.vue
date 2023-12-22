<!-- eslint-disable vue/no-dupe-v-else-if -->
<template>
  <template v-if="listMode.is('list')">
    <q-list data-cy="mode-list">
      <q-item v-for="(a, i) in articles" :key="i" :to="{ name: 'show', params: { user: a.user.id, slug: a.slug } }"
        tag="article">
        <q-item-section side>
          <q-img :src="thumbnailUrl(a)" :ratio="16 / 9" fit="cover" width="240px" height="135px"
            class="bg-grey-1 gt-sm" />
          <q-img :src="thumbnailUrl(a)" :ratio="16 / 9" fit="cover" width="160px" height="90px" class="bg-grey-1 lt-md" />
        </q-item-section>
        <q-item-section top>
          <q-item-label class="text-h4">
            {{ a.title }}
          </q-item-label>
          <q-item-label caption lines="2">{{ description(a) }}</q-item-label>
          <q-item-label>
            <category-list :article="a" />
            <tag-list :article="a" />
          </q-item-label>
          <q-item-label caption>
            <router-link class="default-link" :to="{ name: 'user', params: { id: a.user.id } }">
              {{ a.user.name }}
            </router-link>
          </q-item-label>
          <q-item-label caption class="q-pt-sm">
            <content-meta :article="a" />
          </q-item-label>
        </q-item-section>
      </q-item>
      <q-item v-show="!articles.length">
        <q-item-section>記事がありません</q-item-section>
      </q-item>
    </q-list>
  </template>
  <template v-else-if="listMode.is('gallery')">
    <div class="q-col-gutter-md row items-start" data-cy="mode-gallery">
      <article v-for="(a, i) in articles" :key="i" class="col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
        <router-link :to="{ name: 'show', params: { user: a.user_id, slug: a.slug } }">
          <q-img :to="{ name: 'show', params: { user: a.user.id, slug: a.slug } }" :src="thumbnailUrl(a)" width="100%"
            :ratio="16 / 9" fit="cover" class="bg-grey-1">
            <div class="text-h5 absolute-full flex flex-center">
              {{ a.title }}
            </div>
          </q-img>
        </router-link>
      </article>
      <q-list>
        <q-item v-show="!articles.length">
          <q-item-section>記事がありません</q-item-section>
        </q-item>
      </q-list>
    </div>
  </template>
  <template v-else>
    <q-list separator data-cy="mode-show">
      <template v-for="(a, i) in articles" :key="i">
        <q-item>
          <front-article-show :article="a" />
        </q-item>
      </template>
      <q-item v-show="!articles.length">
        <q-item-section>記事がありません</q-item-section>
      </q-item>
    </q-list>
  </template>
</template>
<script>
import { defineComponent } from 'vue';
import { useListModeStore } from 'src/store/listMode';
import { useMarkdown } from 'src/composables/markdown';
import CategoryList from 'src/components/Front/CategoryList.vue';
import TagList from 'src/components/Front/TagList.vue';
import ContentMeta from 'src/components/Front/Content/ContentMeta.vue';
import FrontArticleShow from 'src/components/Front/FrontArticleShow.vue';
import { DEFAULT_THUMBNAIL } from 'src/const';

const sectionTextableTypes = ['caption', 'text', 'url'];

export default defineComponent({
  name: 'FrontArticleList',
  props: {
    articles: {
      type: Array,
      default: () => [],
    },
  },
  setup() {
    const { render, sanitizeAll } = useMarkdown();
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
        || DEFAULT_THUMBNAIL;
    };

    const listMode = useListModeStore();
    return {
      thumbnailUrl,
      description,
      listMode,
    };
  },
  components: {
    // eslint-disable-next-line vue/no-unused-components
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
