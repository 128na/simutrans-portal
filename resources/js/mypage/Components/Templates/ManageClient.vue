<template>
  <div>
    <b-modal
      id="modal-client-editor"
      :title="item.id ? 'クライアント更新' : 'クライアント作成'"
      :ok-title="item.id ? '更新' : '作成'"
      cancel-title="キャンセル"
      @ok.prevent="handleUpdateOrStore"
    >
      <b-form-group label="クライアント名">
        <b-input v-model="item.name" />
      </b-form-group>
      <b-form-group
        label="リダイレクト先URL一覧"
        description="複数指定する場合はカンマ区切りで入力してください"
      >
        <b-input v-model="item.redirect" />
      </b-form-group>
    </b-modal>

    <b-modal id="modal-client-result" title="クライアント情報" :ok-only="true">
      <b-form-group label="クライアントシークレット">
        <b-input :value="plainSecret" readonly />
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
          <dt>リダイレクトURL</dt>
          <dd>
            {{ item.redirect }}
          </dd>
          <dt>作成日時</dt>
          <dd>
            {{ toDateTime(item.created_at, "fromISO") }}
          </dd>
          <dt>更新日時</dt>
          <dd>
            {{ toDateTime(item.updated_at, "fromISO") }}
          </dd>
          <dt>有効状態</dt>
          <dd>{{ item.revoked ? "無効" : "有効" }}</dd>
          <dt>操作</dt>
          <dd>
            <b-button variant="secondary" size="sm" @click="handleEdit(item)">
              編集
            </b-button>
            <b-button variant="danger" size="sm" @click="handleDelete(item)">
              削除
            </b-button>
          </dd>
        </dl>
      </div>
      <div v-show="!items.length">認証クライアントはありません。</div>
    </div>
    <loading v-else />
  </div>
</template>
<script>
import manage from "../../mixins/manage";
export default {
  mixins: [manage],
  data() {
    return {
      plainSecret: null,
      modal: "client",
      endpoint: "/oauth/clients",
    };
  },
  methods: {
    onCreated(data) {
      this.plainSecret = data.plainSecret;
      this.$bvModal.show("modal-client-result");
    },
  },
};
</script>
