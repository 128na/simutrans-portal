<template>
  <article>
    <text-title>
      {{ article.title || '名もなき一般記事' }}
    </text-title>
    <content-thumbnail :article="article" />

    <dl>
      <dt>投稿者</dt>
      <dd>
        <router-link class="default-link"
          :to="{ name: 'user', params: { idOrNickname: article.user.nickname || article.user.id } }">
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
    <RelatedArticles v-if="article.articles.length" :articles="article.articles" />
    <RelatedArticles v-if="article.relatedArticles.length" :articles="article.relatedArticles">関連付けられた記事
    </RelatedArticles>
    <RelatedScreenshots v-if="article.relatedScreenshots.length" :screenshots="article.relatedScreenshots" />
    <content-meta :article="article" class="q-pt-md" />
  </article>
</template>
<script>
import { defineComponent } from 'vue';
import CategoryList from 'src/components/Front/CategoryList';
import ContentMeta from 'src/components/Front/Content/ContentMeta.vue';
import ContentPage from 'src/components/Front/Content/ContentPage.vue';
import ContentThumbnail from 'src/components/Front/Content/ContentThumbnail.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import RelatedArticles from 'src/components/Common/Screenshot/RelatedArticles.vue';
import RelatedScreenshots from 'src/components/Common/Screenshot/RelatedScreenshots.vue';

export default defineComponent({
  name: 'ArticleShowPage',
  components: {
    RelatedArticles,
    RelatedScreenshots,
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
