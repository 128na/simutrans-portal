<template>
  <q-table :rows="mypage.articles" :columns="columns" :rows-per-page-options="[0]" title="記事一覧"
    no-results-label="該当記事がありません" no-data-label="記事がありません" row-key="id" @row-click.stop="handleClick">
    <template v-slot:body-cell-selected="props">
      <q-td :props="props">
        <q-icon v-show="analytics.selected(props.row.id)" name="check_circle" color="positive" size="sm" />
      </q-td>
    </template>
    <template v-slot:header-cell-selected="props">
      <q-th :props="props">
        <q-icon name="clear" size="sm" class="cursor-pointer" @click="analytics.deselect" color="secondary">
          <q-tooltip>全て選択解除</q-tooltip>
        </q-icon>
      </q-th>
    </template>
  </q-table>
</template>
<script>
import { useMypageStore } from 'src/store/mypage';
import { useAnalyticsStore } from 'src/store/analytics';
import { defineComponent } from 'vue';
import { DateTime } from 'luxon';

const columns = [
  {
    name: 'selected',
    sortable: false,
    align: 'center',
  },
  {
    name: 'id',
    field: 'id',
    label: 'ID',
    sortable: true,
    align: 'center',
  },
  {
    name: 'title',
    field: 'title',
    label: 'タイトル',
    sortable: true,
    align: 'left',
  },
  {
    name: 'published_at',
    field: (row) => DateTime.fromISO(row.published_at).toLocaleString(DateTime.DATETIME_SHORT),
    label: '投稿日時',
    sortable: true,
    align: 'left',
    desc: '記事の投稿（予約）日時',
  },
  {
    name: 'modified_at',
    field: (row) => DateTime.fromISO(row.modified_at).toLocaleString(DateTime.DATETIME_SHORT),
    label: '最終更新日時',
    sortable: true,
    align: 'left',
    desc: '記事の最終更新日時',
  },
];

export default defineComponent({
  name: 'AnylticsTable',
  components: {},
  setup() {
    const mypage = useMypageStore();
    const analytics = useAnalyticsStore();

    const handleClick = (event, row) => {
      analytics.toggleId(row.id);
    };

    return {
      mypage,
      analytics,
      columns,
      handleClick,
    };
  },
});
</script>
