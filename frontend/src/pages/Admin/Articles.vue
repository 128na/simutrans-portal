<template>
  <q-page class="q-ma-md">
    <text-title>記事管理</text-title>
    <q-table :rows="articles" :columns="columns" row-key="id" :filter="filter" :rows-per-page-options="[20, 0]">
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
import { POST_TYPES, STATUSES } from 'src/const';

const columns = [
  {
    name: 'id',
    label: 'ID',
    field: 'id',
    sortable: true,
    align: 'left',
  },
  {
    name: 'post_type',
    label: '形式',
    field: (row) => POST_TYPES[row.post_type],
    sortable: true,
    align: 'left',
  },
  {
    name: 'title',
    label: 'Title',
    field: 'title',
    sortable: true,
    align: 'left',
  },
  {
    name: 'status',
    label: 'Status',
    field: (row) => STATUSES[row.status],
    sortable: true,
    align: 'left',
  },
  {
    name: 'user',
    label: 'User',
    field: (row) => row.user.name,
    sortable: true,
    align: 'left',
  },
  {
    name: 'published_at',
    label: 'Published at',
    field: (row) => DateTime.fromISO(row.published_at).toLocaleString(DateTime.DATETIME_SHORT),
    sortable: true,
    align: 'left',
  },
  {
    name: 'modified_at',
    label: 'Modified at',
    field: (row) => DateTime.fromISO(row.modified_at).toLocaleString(DateTime.DATETIME_SHORT),
    sortable: true,
    align: 'left',
  },
];
export default defineComponent({
  name: 'PageAdminArticles',
  components: { TextTitle },
  setup() {
    const api = useAdminApi();

    const articles = ref([]);
    const fetch = async () => {
      const res = await api.fetchArticles();
      articles.value = res.data;
    };
    fetch();

    return {
      filter: ref(''),
      articles,
      columns,
    };
  },
});
</script>
