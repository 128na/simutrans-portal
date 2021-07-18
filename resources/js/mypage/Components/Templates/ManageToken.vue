<template>
  <div v-if="!fetching">
    <div v-for="token in tokens">
      <h6>{{ token.client.name }}</h6>
      <dl class="ml-3 mb-3">
        <dt>ID</dt>
        <dd>{{ token.id }}</dd>
        <dt>権限</dt>
        <dd>{{ token.scopes.join(", ") }}</dd>
        <dt>作成日時</dt>
        <dd>
          {{ toDateTime(token.created_at, "fromSQL") }}
        </dd>
        <dt>更新日時</dt>
        <dd>
          {{ toDateTime(token.updated_at, "fromSQL") }}
        </dd>
        <dt>期限日時</dt>
        <dd>
          {{ toDateTime(token.expires_at, "fromISO") }}
        </dd>
        <dt>有効状態</dt>
        <dd>{{ token.revoked ? "無効" : "有効" }}</dd>
        <dt>操作</dt>
        <dd>
          <b-button variant="danger" size="sm" @click="handleDelete(token)">
            削除
          </b-button>
        </dd>
      </dl>
    </div>
    <div v-show="!tokens.length">アクセストークンはありません。</div>
  </div>
  <loading v-else />
</template>
<script>
import axios from "axios";
import { DateTime } from "luxon";
export default {
  data() {
    return {
      tokens: [],
      fetching: false,
    };
  },
  created() {
    this.fetchTokens();
  },
  methods: {
    toDateTime(str, format = "fromISO") {
      return DateTime[format](str).toLocaleString(DateTime.DATETIME_FULL);
    },
    async fetchTokens() {
      try {
        this.fetching = true;
        const res = await axios.get("/oauth/tokens");
        this.tokens = res.data;
      } finally {
        this.fetching = false;
      }
    },
    async handleDelete(token) {
      if (confirm("削除しますか？")) {
        await axios.delete(`/oauth/tokens/${token.id}`);
        this.fetchTokens();
      }
    },
  },
};
</script>
