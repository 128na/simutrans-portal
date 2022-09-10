<template>
  <article class="addon-post">
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
        <link-internal :href="article.user.url">
          <text-pre :text="article.user.name" />
        </link-internal>
      </dd>
      <template v-if="article.categories.length">
        <dt>
          <text-sub-title text="カテゴリ" />
        </dt>
        <dd>
          <content-categories :article="article" />
        </dd>
      </template>
      <template v-if="article.tags.length">
        <dt>
          <text-sub-title text="タグ" />
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
      <template v-if="file_info">
        <dt>
          <text-sub-title text="添付ファイル情報（ベータ版機能）" />
        </dt>
        <dd>
          <content-file-info :file_info="file_info" />
        </dd>
      </template>
      <dt>
        <text-sub-title text="ダウンロード" />
      </dt>
      <dd>
        <content-download :article="article" />
      </dd>
    </dl>
    <content-meta :article="article" />
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
    }
  },
  computed: {
    file_info() {
      if (this.article.contents.file) {
        return this.attachments.find(a => a.id == this.article.contents.file)?.fileInfo;
      }
      return null;
    }
  }
};
</script>
