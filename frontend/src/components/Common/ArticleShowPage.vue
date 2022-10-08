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
    <content-page :article="article" :attachments="article.attachments" />
    <content-meta :article="article" />
  </article>
</template>
<script>
import { defineComponent } from 'vue';
import CategoryList from './CategoryList';
import ContentMeta from './ContentMeta.vue';
import ContentPage from './ContentPage.vue';
import ContentThumbnail from './ContentThumbnail.vue';
import TextTitle from './TextTitle.vue';

export default defineComponent({
  name: 'ArticleShowPage',
  components: {
    CategoryList,
    ContentMeta,
    ContentThumbnail,
    ContentPage,
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
