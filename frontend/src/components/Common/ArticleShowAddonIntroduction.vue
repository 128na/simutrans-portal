<template>
  <article>
    <h4 class="text-h4">
      {{article.title}}
    </h4>
    <img :src="thumbnailUrl" class="thumbnai" />

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
      <template v-if="article.tags.length">
        <dt>タグ</dt>
        <dd>
          <tag-list :article="article" />
        </dd>
      </template>
      <dt>説明</dt>
      <dd>
        <text-pre>{{article.contents.description}}</text-pre>
      </dd>
      <template v-if="article.contents.thanks">
        <dt>謝辞・参考にしたアドオン</dt>
        <dd>
          <text-pre>{{article.contents.thanks}}</text-pre>
        </dd>
      </template>
      <template v-if="article.contents.license">
        <dt>ライセンス</dt>
        <dd>
          <text-pre>{{article.contents.license}}</text-pre>
        </dd>
      </template>
      <template v-if="article.contents.agreement">
        <dt>
          掲載許可
        </dt>
        <dd>
          <content-agreement :article="article" />
        </dd>
      </template>
      <dt>掲載先URL</dt>
      <dd>
        <content-link :article="article" />
      </dd>
    </dl>
    <content-meta :article="article" />
  </article>
</template>
<script>
import { defineComponent, computed } from 'vue';
import CategoryList from './CategoryList';
import TagList from './TagList';
import ContentAgreement from './ContentAgreement';
import TextPre from './TextPre.vue';
import ContentLink from './ContentLink.vue';
import ContentMeta from './ContentMeta.vue';

const thumbnailUrl = (article) => {
  const attachmentId = parseInt(article.contents.thumbnail, 10);
  return article.attachments.find((a) => a.id === attachmentId)?.url
    || `${process.env.BACKEND_URL}/storage/default/image.png`;
};

export default defineComponent({
  name: 'ArticleShowAddonIntroduction',
  components: {
    CategoryList,
    TagList,
    ContentAgreement,
    TextPre,
    ContentLink,
    ContentMeta,
  },
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    return {
      thumbnailUrl: computed(() => thumbnailUrl(props.article)),
    };
  },
});
</script>
<style lang="scss">
.thumbnai {
  max-width: 100%;
}
</style>
