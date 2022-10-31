<template>
  <q-page class="q-ma-md">
    <text-title>ユーザー管理</text-title>
    <q-table :rows="users" :columns="columns" row-key="id" :filter="filter" :rows-per-page-options="[20, 0]">
      <template v-slot:top-right>
        <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>
    </q-table>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/TextTitle.vue';
import { defineComponent, ref } from 'vue';
import { useAdminApi } from 'src/composables/api';
import { DateTime } from 'luxon';

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
  components: { TextTitle },
  setup() {
    const api = useAdminApi();

    const users = ref([]);
    const fetch = async () => {
      const res = await api.fetchUsers();
      users.value = res.data;
    };
    fetch();

    return {
      filter: ref(''),
      users,
      columns,
    };
  },
});
</script>
