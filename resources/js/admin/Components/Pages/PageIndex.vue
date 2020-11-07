<template>
  <div>
    <page-title>デバッグ機能</page-title>
    <page-description>
      APIレスポンスは面倒なのでコンソール出力で確認する。
    </page-description>
    <div class="mb-4">
      <b-button variant="danger" @click="handleFlushCache">
        キャッシュ削除
      </b-button>
    </div>

    <div class="mb-4">
      <b-button variant="danger" @click="handleDebug('error')">Error</b-button>
      <b-button variant="danger" @click="handleDebug('warning')">
        Warning
      </b-button>
      <b-button variant="danger" @click="handleDebug('notice')">
        Notice
      </b-button>
    </div>
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
export default {
  methods: {
    ...mapActions(["flushCache", "fetchDebug"]),
    handleFlushCache() {
      this.flushCache();
    },
    handleDebug(level) {
      this.fetchDebug(level);
    },
  },
};
</script>
