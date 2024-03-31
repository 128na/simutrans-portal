<template>
  <q-page class="q-ma-md">
    <text-title>記事管理</text-title>
    <admin-table :rows="articles" :columns="columns">
      <template v-slot="{ props }">
        <q-btn-group>
          <q-btn :disable="props.row.status !== 'publish'" label="表示" @click="handleShow(props.row)" />
          <q-btn v-if="!!props.row.pr" label="PR解除" @click="handleRemovePR(props.row)" />
          <q-btn v-else label="PR追加" @click="handleAddPR(props.row)" />
          <q-btn v-if="!!props.row.deleted_at" label="復元" @click="handleRestore(props.row)" />
          <q-btn v-else label="削除" @click="handleDelete(props.row)" />
          <q-btn v-if="props.row.status === 'publish'" label="非公開" @click="handlePrivate(props.row)" />
          <q-btn v-else label="公開" @click="handlePublish(props.row)" />
        </q-btn-group>
      </template>
    </admin-table>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { defineComponent, ref } from 'vue';
import { useAdminApi } from 'src/composables/api';
import { DateTime } from 'luxon';
import { POST_TYPES, STATUSES } from 'src/const';
import AdminTable from 'src/components/Admin/AdminTable.vue';
import { useAppInfo } from 'src/composables/appInfo';
import { useAuthStore } from 'src/store/auth';
import { useMeta } from 'src/composables/meta';

const columns = [
  {
    name: 'id',
    label: 'ID',
    field: 'id',
    sortable: true,
    align: 'left',
  },
  {
    name: 'pr',
    label: 'PR',
    field: (row) => (row.pr ? '✔' : ''),
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
    field: (row) => (row.published_at
      ? DateTime.fromISO(row.published_at).toLocaleString(DateTime.DATETIME_SHORT)
      : '-'),
    sortable: true,
    align: 'left',
  },
  {
    name: 'modified_at',
    label: 'Modified at',
    field: (row) => (row.modified_at
      ? DateTime.fromISO(row.modified_at).toLocaleString(DateTime.DATETIME_SHORT)
      : '-'),
    sortable: true,
    align: 'left',
  },
  {
    name: 'deleted_at',
    label: 'Deleted_at at',
    field: (row) => (row.deleted_at
      ? DateTime.fromISO(row.deleted_at).toLocaleString(DateTime.DATETIME_SHORT)
      : '-'),
    sortable: true,
    align: 'left',
  },
];

export default defineComponent({
  name: 'PageAdminArticles',
  components: { TextTitle, AdminTable },
  setup() {
    const auth = useAuthStore();
    auth.validateAuth();

    const meta = useMeta();
    meta.setTitle('記事管理');

    const api = useAdminApi();

    const articles = ref([]);
    const fetch = async () => {
      const res = await api.fetchArticles();
      articles.value = res.data;
    };
    fetch();

    const { backendUrl } = useAppInfo();
    const handleShow = (article) => {
      window.open(`${backendUrl}/users/${article.user.id}/${article.slug}`);
    };

    const deleteArticle = async (id) => {
      const res = await api.deleteArticle(id);
      articles.value = res.data;
    };
    const handleRestore = (article) => {
      // eslint-disable-next-line no-alert
      if (window.confirm('復元しますか？')) {
        deleteArticle(article.id);
      }
    };
    const handleDelete = (article) => {
      // eslint-disable-next-line no-alert
      if (window.confirm('論理削除しますか')) {
        deleteArticle(article.id);
      }
    };
    const updateArticle = async (id, params) => {
      const res = await api.putArticle(id, params);
      articles.value = res.data;
    };
    const handlePrivate = (article) => {
      // eslint-disable-next-line no-alert
      if (window.confirm('ステータスを非公開にしますか')) {
        updateArticle(article.id, { article: { status: 'private', pr: article.pr } });
      }
    };
    const handlePublish = (article) => {
      // eslint-disable-next-line no-alert
      if (window.confirm('ステータスを公開にしますか')) {
        updateArticle(article.id, { article: { status: 'publish', pr: article.pr } });
      }
    };
    const handleAddPR = (article) => {
      updateArticle(article.id, { article: { status: article.status, pr: true } });
    };
    const handleRemovePR = (article) => {
      updateArticle(article.id, { article: { status: article.status, pr: false } });
    };

    return {
      articles,
      columns,
      handleShow,
      handleRestore,
      handleDelete,
      handlePrivate,
      handlePublish,
      handleAddPR,
      handleRemovePR,
    };
  },
});
</script>
