<template>
  <div v-if="!fetching">
    <div v-for="item in items">
      <h6>{{ item.client.name }}</h6>
      <dl class="ml-3 mb-3">
        <dt>ID</dt>
        <dd>{{ item.id }}</dd>
        <dt>権限</dt>
        <dd>
          <div v-for="scope in item.scopes">{{ scopeName(scope) }}</div>
        </dd>
        <dt>作成日時</dt>
        <dd>
          {{ toDateTime(item.created_at, "fromSQL") }}
        </dd>
        <dt>更新日時</dt>
        <dd>
          {{ toDateTime(item.updated_at, "fromSQL") }}
        </dd>
        <dt>期限日時</dt>
        <dd>
          {{ toDateTime(item.expires_at, "fromISO") }}
        </dd>
        <dt>有効状態</dt>
        <dd>{{ item.revoked ? "無効" : "有効" }}</dd>
        <dt>操作</dt>
        <dd>
          <b-button variant="danger" size="sm" @click="handleDelete(item)">
            削除
          </b-button>
        </dd>
      </dl>
    </div>
    <div v-show="!items.length">アクセストークンはありません。</div>
  </div>
  <loading v-else />
</template>
<script>
import axios from "axios";
import { DateTime } from "luxon";
export default {
  props: {
    scopes: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data() {
    return {
      items: [],
      fetching: false,
    };
  },
  created() {
    this.fetch();
  },
  methods: {
    toDateTime(str, format = "fromISO") {
      return DateTime[format](str).toLocaleString(DateTime.DATETIME_FULL);
    },
    scopeName(scope) {
      const s = this.scopes.find((s) => s.id === scope);
      if (s) {
        return s.description;
      }
      return scope;
    },
    async fetch() {
      try {
        this.fetching = true;
        const res = await axios.get("/oauth/tokens");
        this.items = res.data;
      } finally {
        this.fetching = false;
      }
    },
    async handleDelete(item) {
      if (confirm("削除しますか？")) {
        await axios.delete(`/oauth/tokens/${item.id}`);
        this.fetch();
      }
    },
  },
};
</script>
