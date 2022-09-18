<template>
  <article class="addon-introduction">
    <content-title :article="article" />
    <content-thumbnail :article="article" :attachments="attachments" />

    <dl>
      <dt>
        <text-sub-title text="作者" />
      </dt>
      <dd>
        <text-pre :text="article.contents.author" />
      </dd>
      <dt>
        <text-sub-title text="投稿者" />
      </dt>
      <dd>
        <content-user :user="article.user" :preview="preview" />
      </dd>
      <template v-if="article.categories.length">
        <dt>
          <text-sub-title text="カテゴリ" />
        </dt>
        <dd>
          <content-categories :article="article" :preview="preview" />
        </dd>
      </template>
      <template v-if="article.tags.length">
        <dt>
          <text-sub-title text="タグ" :preview="preview" />
        </dt>
        <dd>
          <content-tags :article="article" />
        </dd>
      </template>
      <dt>
        <text-sub-title text="説明" />
      </dt>
      <dd>
        <text-pre :text="article.contents.description" />
      </dd>
      <template v-if="article.contents.thanks">
        <dt>
          <text-sub-title text="謝辞・参考にしたアドオン" />
        </dt>
        <dd>
          <text-pre :text="article.contents.thanks" />
        </dd>
      </template>
      <template v-if="article.contents.license">
        <dt>
          <text-sub-title text="ライセンス" />
        </dt>
        <dd>
          <text-pre :text="article.contents.license" />
        </dd>
      </template>
      <template v-if="article.contents.agreement">
        <dt>
          <text-sub-title text="掲載許可" />
        </dt>
        <dd>
          <content-agreement :article="article" />
        </dd>
      </template>
      <dt>
        <text-sub-title text="掲載先URL" />
      </dt>
      <dd>
        <content-link :article="article" />
      </dd>
    </dl>
    <content-meta :article="article" class="mt-4" />
  </article>
</template>
<script>
export default {
  props: {
    article: {
      type: Object,
      required: true
    },
    attachments: {
      type: Array,
      default: () => []
    },
    preview: {
      type: Boolean,
      default: false
    }
  }
};
</script>
