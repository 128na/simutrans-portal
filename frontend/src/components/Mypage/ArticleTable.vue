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
    :rows-per-page-options="[20, 50, 100, 0]" rows-per-page-label="表示件数" no-results-label="該当記事がありません"
    no-data-label="記事がありません" row-key="id" @row-click.stop="handleClick" @row-dblclick.stop="handleDoubleClick">
    <template v-slot:top>
      <div class="q-table__title">記事一覧</div>
      <q-space />
      <q-input debounce="300" color="primary" v-model="filter" placeholder="絞り込み検索">
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </template>

    <template v-slot:body="props">
      <q-tr :props="props" @click="onRowClick(props.row)" :class="colorClass(props)">
        <template v-for="(col) in props.cols" :key="col.name">
          <q-td :props="props">{{
            (typeof col.field) === 'function' ? col.field(props.row) : props.row[col.field]
            }}</q-td>
        </template>
      </q-tr>
    </template>
  </q-table>
  <q-dialog v-model="dialogShow">
    <dialog-menu :row=dialogRow @close="dialogShow = false" />
  </q-dialog>
</template>

<script>
import { useQuasar } from 'quasar';
import { useMypageStore } from 'src/store/mypage';
import {
  POST_TYPES, STATUSES, ARTICLE_OPTIONS, ARTICLE_COLUMNS,
} from 'src/const';
import {
  defineComponent, computed, ref, watch,
} from 'vue';
import { useRouter } from 'vue-router';
import DialogMenu from 'src/components/Mypage/DialogMenu.vue';

export default defineComponent({
  name: 'ArticleTable',
  setup() {
    const store = useMypageStore();
    const filter = ref('');
    const rows = computed(() => store.articles.filter((a) => {
      if (!filter.value) {
        return true;
      }

      return String(a.id).includes(filter.value)
        || a.title.includes(filter.value)
        || STATUSES[a.status].includes(filter.value)
        || POST_TYPES[a.post_type].includes(filter.value);
    }));
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

    const colorClass = (props) => {
      if (props.row.status === 'publish') {
        return '';
      }
      if (props.row.status === 'reservation') {
        return 'bg-blue-2';
      }

      return 'bg-grey-4';
    };

    return {
      rows,
      columns: ARTICLE_COLUMNS,
      pagination,
      visibleColumns,
      options: ARTICLE_OPTIONS,
      dialogRow,
      dialogShow,
      handleClick,
      handleDoubleClick,
      filter,
      colorClass,
    };
  },
  components: { DialogMenu },
});
</script>
