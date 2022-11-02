<template>
  <article>
    <text-title>
      {{ article.title || '名もなき一般記事' }}
    </text-title>
    <content-thumbnail :article="article" />

    <dl>
      <dt>作者</dt>
      <dd>{{ article.contents.author || '未設定' }}</dd>
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
    <content-page :article="article" :attachments="article.attachments" />
    <content-meta :article="article" />
  </article>
</template>
<script>
import { defineComponent } from 'vue';
import CategoryList from 'src/components/Common/CategoryList';
import ContentMeta from 'src/components/Common/Content/ContentMeta.vue';
import ContentPage from 'src/components/Common/Content/ContentPage.vue';
import ContentThumbnail from 'src/components/Common/Content/ContentThumbnail.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';

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
