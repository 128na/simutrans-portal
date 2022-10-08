<template>
  <article>
    <text-title>
      {{article.title}}
    </text-title>
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
import TextTitle from './TextTitle.vue';

export default defineComponent({
  name: 'ArticleShowMarkdown',
  components: {
    CategoryList,
    ContentMeta,
    ContentThumbnail,
    ContentMarkdown,
    TextTitle,
  },
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
});
</script>
