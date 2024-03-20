<template>
  <article>
    <text-title>
      {{ article.title || '名もなきアドオン投稿' }}
    </text-title>
    <content-thumbnail :article="article" />

    <dl>
      <dt>作者</dt>
      <dd>{{ article.contents.author || '未設定' }}</dd>
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
      <template v-if="article.tags.length">
        <dt>タグ</dt>
        <dd>
          <tag-list :article="article" />
        </dd>
      </template>
      <dt>説明</dt>
      <dd>
        <text-pre>{{ article.contents.description }}</text-pre>
      </dd>
      <template v-if="article.contents.thanks">
        <dt>謝辞・参考にしたアドオン</dt>
        <dd>
          <text-pre>{{ article.contents.thanks }}</text-pre>
        </dd>
      </template>
      <template v-if="article.contents.license">
        <dt>ライセンス</dt>
        <dd>
          <text-pre>{{ article.contents.license }}</text-pre>
        </dd>
      </template>
      <RelatedArticles v-if="article.articles.length" :articles="article.articles" />
      <RelatedArticles v-if="article.relatedArticles.length" :articles="article.relatedArticles">関連付けられた記事
      </RelatedArticles>
      <RelatedScreenshots v-if="article.relatedScreenshots.length" :screenshots="article.relatedScreenshots" />
      <template v-if="article.contents.file">
        <dt>ファイル一覧</dt>
        <dd>
          <content-file-info :article="article" />
        </dd>
      </template>
      <dt>ダウンロード</dt>
      <dd>
        <content-download :article="article" />
      </dd>
    </dl>
    <content-meta :article="article" />
  </article>
</template>
<script>
import { defineComponent } from 'vue';
import CategoryList from 'src/components/Front/CategoryList';
import TagList from 'src/components/Front/TagList';
import TextPre from 'src/components/Common/Text/TextPre.vue';
import ContentMeta from 'src/components/Front/Content/ContentMeta.vue';
import ContentThumbnail from 'src/components/Front/Content/ContentThumbnail.vue';
import ContentDownload from 'src/components/Front/Content/ContentDownload.vue';
import ContentFileInfo from 'src/components/Front/Content/ContentFileInfo.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import RelatedArticles from 'src/components/Common/Screenshot/RelatedArticles.vue';
import RelatedScreenshots from 'src/components/Common/Screenshot/RelatedScreenshots.vue';

export default defineComponent({
  name: 'ArticleShowAddonPost',
  components: {
    RelatedArticles,
    RelatedScreenshots,
    CategoryList,
    TagList,
    TextPre,
    ContentMeta,
    ContentThumbnail,
    ContentDownload,
    ContentFileInfo,
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
