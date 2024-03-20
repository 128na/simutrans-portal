<template>
  <article>
    <text-title>
      {{ article.title || '名もなきmarkdown記事' }}
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
    <content-markdown :article="article" />
    <RelatedArticles v-if="article.articles.length" :articles="article.articles" />
    <RelatedArticles v-if="article.relatedArticles?.length" :articles="article.relatedArticles">関連付けられた記事
    </RelatedArticles>
    <RelatedScreenshots v-if="article.relatedScreenshots?.length" :screenshots="article.relatedScreenshots" />
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
import RelatedArticles from 'src/components/Common/Screenshot/RelatedArticles.vue';
import RelatedScreenshots from 'src/components/Common/Screenshot/RelatedScreenshots.vue';

export default defineComponent({
  name: 'ArticleShowMarkdown',
  components: {
    RelatedArticles,
    RelatedScreenshots,
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
