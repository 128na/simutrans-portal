<template>
  <article>
    <h4 class="text-h4">
      {{article.title}}
    </h4>
    <content-thumbnail :article="article" />

    <dl>
      <dt>作者</dt>
      <dd>{{article.contents.author}}</dd>
      <dt>投稿者</dt>
      <dd>
        <router-link class="default-link" :to="{name:'user', params:{id:article.user.id}}">
          {{ article.user.name }}
        </router-link>
      </dd>
      <template v-if="article.categories.length">
        <dt>カテゴリ</dt>
        <dd>
          <category-list :article="article" />
        </dd>
      </template>
    </dl>
    <content-markdown :article="article" />
    <content-meta :article="article" />
  </article>
</template>
<script>
import { defineComponent } from 'vue';
import CategoryList from './CategoryList';
import ContentMeta from './ContentMeta.vue';
import ContentThumbnail from './ContentThumbnail.vue';
import ContentMarkdown from './ContentMarkdown.vue';

export default defineComponent({
  name: 'ArticleShowMarkdown',
  components: {
    CategoryList,
    ContentMeta,
    ContentThumbnail,
    ContentMarkdown,
  },
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
});
</script>
