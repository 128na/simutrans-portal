<template>
  <q-expansion-item label="表示カラム設定" switch-toggle-side>
    <div class="q-ma-sm">
      <q-option-group :options="options" v-model="visibleColumns" type="checkbox">
        <template v-slot:label="props">
          <q-item>
            <q-item-section>
              <q-item-label>{{ props.label }}</q-item-label>
              <q-item-label caption>{{ props.desc }}</q-item-label>
            </q-item-section>
          </q-item>
        </template>
      </q-option-group>
    </div>
  </q-expansion-item>
  <q-table v-model:pagination="pagination" :rows="rows" :columns="columns" :visible-columns="visibleColumns"
    :rows-per-page-options="[20, 50, 100, 0]" title="記事一覧" rows-per-page-label="表示件数" no-results-label="該当記事がありません"
    no-data-label="記事がありません" row-key="id" @row-click.stop="handleClick" @row-dblclick.stop="handleDoubleClick">
  </q-table>
  <q-dialog v-model="dialogShow">
    <dialog-menu :row=dialogRow @close="dialogShow = false" />
  </q-dialog>
</template>

<script>
import { DateTime } from 'luxon';
import { useQuasar } from 'quasar';
import { useMypageStore } from 'src/store/mypage';
import { POST_TYPES, STATUSES } from 'src/const';
import {
  defineComponent, computed, ref, watch,
} from 'vue';
import { useRouter } from 'vue-router';
import DialogMenu from 'src/components/Mypage/DialogMenu.vue';

const columns = [
  {
    name: 'id',
    field: 'id',
    label: 'ID',
    sortable: true,
    align: 'center',
    desc: '記事のID',
  },
  {
    name: 'status',
    field: (row) => STATUSES[row.status],
    label: 'ステータス',
    sortable: true,
    align: 'left',
    desc: '記事の公開状態',
  },
  {
    name: 'post_type',
    field: (row) => POST_TYPES[row.post_type],
    label: '形式',
    sortable: true,
    align: 'left',
    desc: '記事の形式',
  },
  {
    name: 'title',
    field: 'title',
    label: 'タイトル',
    sortable: true,
    align: 'left',
    desc: '記事のタイトル',
  },
  {
    name: 'totalViewCount',
    field: (row) => row.metrics.totalViewCount,
    label: 'PV',
    sortable: true,
    desc: '記事の個別ページ表示回数。トップや記事一覧での表示回数は含みません。',
  },
  {
    name: 'totalConversionCount',
    field: (row) => row.metrics.totalConversionCount,
    label: 'CV',
    sortable: true,
    desc: 'アドオンのダウンロード、掲載URLのクリック回数',
  },
  {
    name: 'published_at',
    field: (row) => (row.published_at ? DateTime.fromISO(row.published_at).toLocaleString(DateTime.DATETIME_SHORT) : '-'),
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

const options = columns.map((c) => ({
  label: c.label,
  value: c.name,
  desc: c.desc,
}));

export default defineComponent({
  name: 'ArticleTable',
  setup() {
    const store = useMypageStore();
    const rows = computed(() => store.articles);
    const pagination = ref({
      sortBy: 'id',
      descending: true,
      page: 1,
      rowsPerPage: 20,
    });

    const $q = useQuasar();
    const visibleColumns = ref(
      $q.localStorage.getItem('mypage.visibleColumns')
      || ['id', 'status', 'post_type', 'title', 'totalViewCount', 'totalConversionCount', 'published_at', 'modified_at'],
    );
    watch(visibleColumns, (val) => {
      $q.localStorage.set('mypage.visibleColumns', val);
    });
    const dialogRow = ref();
    const dialogShow = computed({
      get: () => !!dialogRow.value,
      set: () => { dialogRow.value = null; },
    });
    const router = useRouter();
    let timer = null;
    const handleClick = (event, row) => {
      timer = setTimeout(() => {
        dialogRow.value = row;
      }, 150);
    };
    const handleDoubleClick = (event, row) => {
      if (timer) {
        clearTimeout(timer);
      }
      router.push({ name: 'edit', params: { id: row.id } });
    };
    return {
      rows,
      columns,
      pagination,
      visibleColumns,
      options,
      dialogRow,
      dialogShow,
      handleClick,
      handleDoubleClick,
    };
  },
  components: { DialogMenu },
});
</script>
