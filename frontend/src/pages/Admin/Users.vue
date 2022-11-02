<template>
  <q-page class="q-ma-md">
    <text-title>ユーザー管理</text-title>
    <admin-table :rows="users" :columns="columns">
      <template v-slot="{ props }">
        <q-btn-group class="q-mb-md">
          <q-btn v-if="!!props.row.deleted_at" label="復元" @click="handleRestore(props.row)" />
          <q-btn v-else label="削除" @click="handleDelete(props.row)" />
        </q-btn-group>

        <div>
          <div>招待先</div>
          <pre>{{ childrenMap(props.row) || 'なし' }}</pre>
        </div>
        <div>
          <div>招待元</div>
          <pre>{{ parentMap(props.row) || 'なし' }}</pre>
        </div>
      </template>
    </admin-table>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { defineComponent, ref } from 'vue';
import { useAdminApi } from 'src/composables/api';
import { DateTime } from 'luxon';
import AdminTable from 'src/components/Admin/AdminTable.vue';

const columns = [
  {
    name: 'id',
    label: 'ID',
    field: 'id',
    sortable: true,
    align: 'left',
  },
  {
    name: 'name',
    label: 'Name',
    field: 'name',
    sortable: true,
    align: 'left',
  },
  {
    name: 'email',
    label: 'Email',
    field: 'email',
    sortable: true,
    align: 'left',
  },
  {
    name: 'role',
    label: 'Role',
    field: 'role',
    sortable: true,
    align: 'left',
  },
  {
    name: 'role',
    label: 'Role',
    field: 'role',
    sortable: true,
    align: 'left',
  },
  {
    name: 'articles',
    label: 'Articles',
    field: 'articles_count',
    sortable: true,
    align: 'left',
  },
  {
    name: 'created',
    label: 'Created at',
    field: (row) => DateTime.fromISO(row.created_at).toLocaleString(DateTime.DATETIME_SHORT),
    sortable: true,
    align: 'left',
  },
  {
    name: 'email',
    label: 'Updated at',
    field: (row) => DateTime.fromISO(row.updated_at).toLocaleString(DateTime.DATETIME_SHORT),
    sortable: true,
    align: 'left',
  },
  {
    name: 'email',
    label: 'Verified at',
    field: (row) => (row.email_verified_at
      ? DateTime.fromISO(row.email_verified_at).toLocaleString(DateTime.DATETIME_SHORT)
      : '-'),
    sortable: true,
    align: 'left',
  },
  {
    name: 'email',
    label: 'Deleted at',
    field: (row) => (row.deleted_at
      ? DateTime.fromISO(row.deleted_at).toLocaleString(DateTime.DATETIME_SHORT)
      : '-'),
    sortable: true,
    align: 'left',
  },
];
export default defineComponent({
  name: 'PageAdminUsers',
  components: { TextTitle, AdminTable },
  setup() {
    const api = useAdminApi();

    const users = ref([]);
    const fetch = async () => {
      const res = await api.fetchUsers();
      users.value = res.data;
    };
    fetch();

    const deleteUser = async (id) => {
      const res = await api.deleteUser(id);
      users.value = res.data;
    };
    const handleRestore = (user) => {
      // eslint-disable-next-line no-alert
      if (window.confirm('復元しますか？')) {
        deleteUser(user.id);
      }
    };
    const handleDelete = (user) => {
      // eslint-disable-next-line no-alert
      if (window.confirm('論理削除しますか')) {
        deleteUser(user.id);
      }
    };

    const findUser = (userId, defaultValue = null) => users.value.find((u) => u.id === userId) || defaultValue;
    const findInvitedReclusive = (userId) => {
      const user = findUser(userId);
      if (user) {
        if (user.invited_by) {
          return [user, ...findInvitedReclusive(user.invited_by)];
        }
        return [user];
      }
      return [];
    };
    const parentMap = (user) => {
      const parents = findInvitedReclusive(user.invited_by);
      return parents.map((u) => u.name).join(' ← ');
    };

    const findInvites = (userId) => users.value.filter((u) => u.invited_by === userId);
    const findInvitesReclusive = (userId) => {
      const invites = findInvites(userId);
      if (invites.length) {
        return invites.map((user) => ({ ...user, invites: findInvitesReclusive(user.id) }));
      }
      return invites;
    };
    const childrenString = (user, level = 1) => {
      if (user.invites.length) {
        const tab = '\t'.repeat(level);
        const children = user.invites
          .map((c) => `${tab}┗${childrenString(c, level + 1)}`)
          .join('\n');
        return `${user.name}\n${children}`;
      }
      return `${user.name}`;
    };
    const childrenMap = (user) => {
      const children = findInvitesReclusive(user.id);
      return children.map((u) => childrenString(u)).join('\n');
    };

    return {
      filter: ref(''),
      users,
      columns,
      handleRestore,
      handleDelete,
      parentMap,
      childrenMap,
    };
  },
});
</script>
