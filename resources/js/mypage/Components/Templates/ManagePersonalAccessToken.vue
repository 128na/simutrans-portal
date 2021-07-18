<template>
  <div>
    <b-modal
      id="modal-pat-editor"
      title="PAT作成"
      ok-title="作成"
      cancel-title="キャンセル"
      @ok="handleUpdateOrStore"
    >
      <b-form-group label="PAT名">
        <b-input v-model="item.name" />
      </b-form-group>

      <b-form-group label="スコープ">
        <b-form-checkbox-group
          v-model="item.scopes"
          :options="optionScopes"
        ></b-form-checkbox-group>
      </b-form-group>
    </b-modal>

    <b-modal id="modal-pat-result" title="トークン情報" :ok-only="true">
      <b-form-group label="アクセストークン">
        <b-input :value="accessToken" readonly />
      </b-form-group>
    </b-modal>

    <b-button variant="primary" class="mb-3" @click="handleCreate">
      新規作成
    </b-button>

    <div v-if="!fetching">
      <div v-for="item in items">
        <h6>{{ item.name }}</h6>
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
      <div v-show="!items.length">認証トークンはありません。</div>
    </div>
    <loading v-else />
  </div>
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
      item: {
        id: null,
        name: "",
        scopes: [],
      },
      accessToken: null,
    };
  },
  created() {
    this.fetch();
  },
  computed: {
    optionScopes() {
      return this.scopes.map((s) => {
        return {
          text: s.description,
          value: s.id,
        };
      });
    },
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
    setItem({ id, name, scopes } = {}) {
      this.item = {
        id: id || null,
        name: name || "",
        scopes: scopes || [],
      };
    },
    async fetch() {
      try {
        this.fetching = true;
        const res = await axios.get("/oauth/personal-access-tokens");
        this.items = res.data;
      } finally {
        this.fetching = false;
      }
    },
    async handleDelete(item) {
      if (confirm("削除しますか？")) {
        await axios.delete(`/oauth/personal-access-tokens/${item.id}`);
        this.fetch();
      }
    },
    handleCreate() {
      this.setItem();
      this.$bvModal.show("modal-pat-editor");
    },
    async handleUpdateOrStore() {
      try {
        const res = await axios.post("/oauth/personal-access-tokens", {
          name: this.item.name,
          scopes: this.item.scopes,
        });
        this.accessToken = res.data.accessToken;
        this.$bvModal.show("modal-pat-result");
        this.setItem();
        this.fetch();
      } catch (e) {
        alert("作成に失敗しました。");
      }
    },
  },
};
</script>
