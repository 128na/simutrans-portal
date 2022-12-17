<template>
  <article>
    <text-title>
      {{ article.title || '名もなきmarkdown記事' }}
    </text-title>
    <content-thumbnail :article="article" />

    <dl>
      <dt>投稿者</dt>
      <dd>
        <router-link class="default-link" :to="{ name: 'user', params: { id: article.user.id } }">
          {{ article.user.name || '未設定' }}
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
import CategoryList from 'src/components/Front/CategoryList';
import ContentMeta from 'src/components/Front/Content/ContentMeta.vue';
import ContentThumbnail from 'src/components/Front/Content/ContentThumbnail.vue';
import ContentMarkdown from 'src/components/Front/Content/ContentMarkdown.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';

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
