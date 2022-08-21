<template>
  <div>
    <page-title>マイページ</page-title>
    <page-description>投稿した記事の管理ができます</page-description>
    <div v-if="ready">
      <div v-if="!isVerified">
        <need-verify />
      </div>
      <div v-else>
        <page-sub-title>投稿記事エクスポート</page-sub-title>
        <page-description>
          投稿した記事を一括でダウンロードできます。<br>
          記事数が多いとファイルの生成には数分かかることがあります。
          <bulk-zip-downloader :target_type="targetType" class="mb-3" />
        </page-description>
        <page-sub-title>投稿一覧</page-sub-title>
        <article-table :articles="articles" />
      </div>
    </div>
    <loading-message v-else />
  </div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
import { validateLogin } from '../../mixins/auth';
import { TARGET_TYPE_USER } from '../../../const';
export default {
  mixins: [validateLogin],
  created() {
    if (this.isLoggedIn && !this.articlesLoaded) {
      this.fetchArticles();
    }
  },
  computed: {
    ...mapGetters([
      'isLoggedIn',
      'initialized',
      'isVerified',
      'articlesLoaded',
      'articles'
    ]),
    ready() {
      return this.articlesLoaded;
    },
    targetType() {
      return TARGET_TYPE_USER;
    }
  },
  methods: {
    ...mapActions(['fetchArticles'])
  }
};
</script>
