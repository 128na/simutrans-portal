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
import { defineComponent } from 'vue';
import CategoryList from './CategoryList';
import TagList from './TagList';
import ContentAgreement from './ContentAgreement';
import TextPre from './TextPre.vue';
import ContentLink from './ContentLink.vue';
import ContentMeta from './ContentMeta.vue';
import ContentThumbnail from './ContentThumbnail.vue';
import TextTitle from './TextTitle.vue';

export default defineComponent({
  name: 'ArticleShowAddonIntroduction',
  components: {
    CategoryList,
    TagList,
    ContentAgreement,
    TextPre,
    ContentLink,
    ContentMeta,
    ContentThumbnail,
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
