<template>
  <div>
    <b-modal
      id="modal-editor"
      :title="client.id ? 'クライアント更新' : 'クライアント作成'"
      :ok-title="client.id ? '更新' : '作成'"
      cancel-title="キャンセル"
      @ok="handleUpdateOrStore"
    >
      <b-form-group label="クライアント名">
        <b-input v-model="client.name" />
      </b-form-group>
      <b-form-group
        label="リダイレクト先URL一覧"
        description="複数指定する場合はカンマ区切りで入力してください"
      >
        <b-input v-model="client.redirect" />
      </b-form-group>
    </b-modal>

    <b-modal id="modal-result" title="クライアント情報" :ok-only="true">
      <b-form-group label="クライアントシークレット">
        <b-input :value="plainSecret" readonly />
      </b-form-group>
    </b-modal>

    <b-button variant="primary" class="mb-3" @click="handleCreate">
      新規作成
    </b-button>

    <div v-if="!fetching">
      <div v-for="client in clients">
        <h6>{{ client.name }}</h6>
        <dl class="ml-3 mb-3">
          <dt>ID</dt>
          <dd>{{ client.id }}</dd>
          <dt>リダイレクトURL</dt>
          <dd>
            {{ client.redirect }}
          </dd>
          <dt>作成日時</dt>
          <dd>
            {{ toDateTime(client.created_at, "fromISO") }}
          </dd>
          <dt>更新日時</dt>
          <dd>
            {{ toDateTime(client.updated_at, "fromISO") }}
          </dd>
          <dt>有効状態</dt>
          <dd>{{ client.revoked ? "無効" : "有効" }}</dd>
          <dt>操作</dt>
          <dd>
            <b-button variant="secondary" size="sm" @click="handleEdit(client)">
              更新
            </b-button>
            <b-button variant="danger" size="sm" @click="handleDelete(client)">
              削除
            </b-button>
          </dd>
        </dl>
      </div>
      <div v-show="!clients.length">認証クライアントはありません。</div>
    </div>
    <loading v-else />
  </div>
</template>
<script>
import axios from "axios";
import { DateTime } from "luxon";
export default {
  data() {
    return {
      clients: [],
      fetching: false,
      client: {
        id: null,
        name: "",
        redirect: "",
      },
      plainSecret: null,
    };
  },
  created() {
    this.fetchClients();
  },
  methods: {
    toDateTime(str, format = "fromISO") {
      return DateTime[format](str).toLocaleString(DateTime.DATETIME_FULL);
    },
    setClient({ id, name, redirect } = {}) {
      this.client = {
        id: id || null,
        name: name || "",
        redirect: redirect || "",
      };
    },
    async fetchClients() {
      try {
        this.fetching = true;
        const res = await axios.get("/oauth/clients");
        this.clients = res.data;
      } finally {
        this.fetching = false;
      }
    },
    async handleDelete(client) {
      if (confirm("削除しますか？")) {
        await axios.delete(`/oauth/clients/${client.id}`);
        this.fetchClients();
      }
    },
    handleCreate() {
      this.setClient();
      this.$bvModal.show("modal-editor");
    },
    handleEdit(client) {
      this.setClient(client);
      this.$bvModal.show("modal-editor");
    },
    async handleUpdateOrStore() {
      if (this.client.id) {
        try {
          await axios.put(`/oauth/clients/${this.client.id}`, {
            name: this.client.name,
            redirect: this.client.redirect,
          });
          return this.fetchClients();
        } catch (e) {
          alert("更新に失敗しました。");
        }
      }

      try {
        const res = await axios.post("/oauth/clients", {
          name: this.client.name,
          redirect: this.client.redirect,
        });
        this.plainSecret = res.data.plainSecret;
        this.$bvModal.show("modal-result");
        this.setClient();
        this.fetchClients();
      } catch (e) {
        alert("作成に失敗しました。");
      }
    },
  },
};
</script>
