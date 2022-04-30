<template>
  <div>
    <page-title>ユーザー一覧</page-title>
    <page-description> ユーザーの論理削除状態を変更できる。 </page-description>
    <b-form
      inline
      class="mb-2"
    >
      <detail-button v-model="detail" />
      <b-form-input
        v-model="search"
        placeholder="絞り込み"
      />
    </b-form>
    <b-table
      hover
      :items="users"
      :fields="computed_fields"
      :filter="search"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      stacked="sm"
      @row-clicked="handleRow"
    >
      <template #cell(invites)="row">
        {{ findInvites(row.item.id).length }}
      </template>
      <template #cell(invited)="row">
        {{ findUser(row.item.invited_by, {}).name }}
      </template>
      <template #cell(action)="row">
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
    <b-modal
      id="user-modal"
      :title="selectedUser.name"
      size="lg"
    >
      <dl>
        <dt>ID</dt>
        <dd>{{ selectedUser.id }}</dd>
        <dt>名前</dt>
        <dd>{{ selectedUser.name }}</dd>
        <dt>メール</dt>
        <dd>{{ selectedUser.email }}</dd>
        <dt>権限</dt>
        <dd>{{ selectedUser.role }}</dd>
        <dt>投稿件数</dt>
        <dd>{{ selectedUser.articles_count }}</dd>
        <dt>登録日</dt>
        <dd>{{ selectedUser.created_at }}</dd>
        <dt>認証日</dt>
        <dd>{{ selectedUser.email_verified_at }}</dd>
        <dt>削除日</dt>
        <dd>{{ selectedUser.deleted_at }}</dd>
        <dt>招待コード</dt>
        <dd>{{ selectedUser.invitation_code }}</dd>
        <dt>招待元</dt>
        <dd>{{ parentMap }}</dd>
        <dt>招待先</dt>
        <dd>
          <pre>{{ childrenMap }}</pre>
        </dd>
      </dl>
    </b-modal>
  </div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
export default {
  data() {
    return {
      sortBy: 'id',
      sortDesc: true,
      search: '',
      detail: false,
      fields: [
        {
          key: 'id',
          label: 'ID',
          sortable: true
        },
        {
          key: 'name',
          label: '名前',
          sortable: false
        },
        {
          key: 'role',
          label: '権限',
          sortable: true
        },
        {
          key: 'articles_count',
          label: '投稿数',
          sortable: true
        },
        {
          key: 'invites',
          label: '招待数',
          sortable: true
        },
        {
          key: 'invited',
          label: '招待者',
          sortable: true
        },
        {
          key: 'email_verified_at',
          label: 'メール認証日時',
          sortable: true
        },
        {
          key: 'created_at',
          label: '作成日時',
          sortable: true
        },
        {
          key: 'updated_at',
          label: '更新日時',
          sortable: true
        },
        {
          key: 'deleted_at',
          label: '削除日時',
          sortable: true
        },
        {
          key: 'action',
          label: '操作',
          sortable: false
        }
      ],
      selectedUser: {}
    };
  },
  created() {
    if (!this.users.length) {
      this.fetchUsers();
    }
  },
  computed: {
    ...mapGetters(['users']),
    computed_fields() {
      const filter_keys = ['id', 'name', 'action'];
      return this.detail
        ? this.fields
        : this.fields.filter((f) => filter_keys.includes(f.key));
    },
    parentMap() {
      const parents = this.findInvitedReclusive(this.selectedUser.invited_by);
      return parents.map((user) => user.name).join(' ← ');
    },
    childrenMap() {
      const children = this.findInvitesReclusive(this.selectedUser.id);
      return children.map((user) => this.childrenString(user)).join('\n');
    }
  },
  methods: {
    ...mapActions(['fetchUsers', 'deleteUser']),
    toRestore(user) {
      if (confirm('復元しますか？')) {
        this.deleteUser(user.id);
      }
    },
    toDestroy(user) {
      if (confirm('論理削除しますか')) {
        this.deleteUser(user.id);
      }
    },
    findInvites(userId) {
      return this.users.filter((u) => u.invited_by === userId);
    },
    findUser(userId, defaultValue = null) {
      return this.users.find((u) => u.id === userId) || defaultValue;
    },
    findInvitedReclusive(userId) {
      const user = this.findUser(userId);
      if (user) {
        if (user.invited_by) {
          return [user, ...this.findInvitedReclusive(user.invited_by)];
        }
        return [user];
      }
      return [];
    },
    findInvitesReclusive(userId) {
      const users = this.findInvites(userId);
      if (users.length) {
        return users.map((user) =>
          Object.assign({}, user, {
            invites: this.findInvitesReclusive(user.id)
          })
        );
      }
      return users;
    },
    handleRow(item, index, event) {
      this.selectedUser = item;
      this.$bvModal.show('user-modal');
    },
    childrenString(user, level = 1) {
      if (user.invites.length) {
        const tab = '\t'.repeat(level);
        const children = user.invites
          .map((c) => `${tab}┗${this.childrenString(c, level + 1)}`)
          .join('\n');
        return `${user.name}\n${children}`;
      }
      return `${user.name}`;
    }
  }
};
</script>
<style lang="scss" scoped>
.action-box {
  width: 8rem;
}
</style>
