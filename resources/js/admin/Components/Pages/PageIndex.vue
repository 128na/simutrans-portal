<template>
  <div>
    <page-title>デバッグ機能</page-title>
    <page-description>
      APIレスポンスは面倒なのでコンソール出力で確認する。
    </page-description>
    <div class="mb-4">
      <b-button
        variant="danger"
        @click="handleFlushCache"
      >
        キャッシュ削除
      </b-button>
    </div>

    <div class="mb-4">
      <b-button
        variant="danger"
        @click="handleDebug('error')"
      >
        Error
      </b-button>
      <b-button
        variant="danger"
        @click="handleDebug('warning')"
      >
        Warning
      </b-button>
      <b-button
        variant="danger"
        @click="handleDebug('notice')"
      >
        Notice
      </b-button>
    </div>
    <page-sub-title>Oauth2</page-sub-title>
    <div class="mb-4">
      <a href="/admin/oauth/twitter/authorize">認証</a><br>
      <a href="/admin/oauth/twitter/refresh">トークンリフレッシュ</a><br>
      <a href="/admin/oauth/twitter/revoke">トークン削除</a><br>
    </div>
  </div>
</template>
<script>
import { mapActions } from 'vuex';
export default {
  methods: {
    ...mapActions(['flushCache', 'fetchDebug']),
    handleFlushCache() {
      this.flushCache();
    },
    handleDebug(level) {
      this.fetchDebug(level);
    }
  }
};
</script>
