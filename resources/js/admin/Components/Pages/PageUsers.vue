<template>
  <div>
    <page-title>ユーザー一覧</page-title>
    <page-description> ユーザーの論理削除状態を変更できる。 </page-description>
    <b-form inline class="mb-2">
      <detail-button v-model="detail" />
      <b-form-input v-model="search" placeholder="絞り込み" />
    </b-form>
    <b-table
      hover
      :items="users"
      :fields="computed_fields"
      :filter="search"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      stacked="sm"
    >
      <template v-slot:cell(action)="row">
        <div v-if="row.item.deleted_at">
          <b-button
            size="sm"
            variant="outline-danger"
            @click="toRestore(row.item)"
          >
            復元
          </b-button>
        </div>
        <div v-else>
          <b-button
            size="sm"
            variant="outline-danger"
            @click="toDestroy(row.item)"
          >
            削除
          </b-button>
        </div>
      </template>
    </b-table>
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
export default {
  data() {
    return {
      sortBy: "id",
      sortDesc: true,
      search: "",
      detail: false,
      fields: [
        {
          key: "id",
          label: "ID",
          sortable: true,
        },
        {
          key: "name",
          label: "名前",
          sortable: false,
        },
        {
          key: "role",
          label: "権限",
          sortable: true,
        },
        {
          key: "articles_count",
          label: "投稿数",
          sortable: true,
        },
        {
          key: "email_verified_at",
          label: "メール認証日時",
          sortable: true,
        },
        {
          key: "created_at",
          label: "作成日時",
          sortable: true,
        },
        {
          key: "updated_at",
          label: "更新日時",
          sortable: true,
        },
        {
          key: "deleted_at",
          label: "削除日時",
          sortable: true,
        },
        {
          key: "action",
          label: "操作",
          sortable: false,
        },
      ],
    };
  },
  created() {
    if (!this.users.length) {
      this.fetchUsers();
    }
  },
  computed: {
    ...mapGetters(["users"]),
    computed_fields() {
      const filter_keys = ["id", "name", "action"];
      return this.detail
        ? this.fields
        : this.fields.filter((f) => filter_keys.includes(f.key));
    },
  },
  methods: {
    ...mapActions(["fetchUsers", "deleteUser"]),
    toRestore(user) {
      if (confirm("復元しますか？")) {
        this.deleteUser(user.id);
      }
    },
    toDestroy(user) {
      if (confirm("論理削除しますか")) {
        this.deleteUser(user.id);
      }
    },
  },
};
</script>
<style lang="scss" scoped>
.action-box {
  width: 8rem;
}
</style>
