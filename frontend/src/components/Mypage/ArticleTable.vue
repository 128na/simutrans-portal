<template>
  <q-expansion-item label="表示カラム設定" switch-toggle-side>
    <q-option-group :options="options" v-model="visibleColumns" type="checkbox">
      <template v-slot:label="props">
        <q-item>
          <q-item-section>
            <q-item-label>{{props.label}}</q-item-label>
            <q-item-label caption>{{props.desc}}</q-item-label>
          </q-item-section>
        </q-item>
      </template>
    </q-option-group>
  </q-expansion-item>
  <q-table v-model:pagination="pagination" :rows="rows" :columns="columns" :visible-columns="visibleColumns"
    :rows-per-page-options="[20,50,100,0]" title="記事一覧" rows-per-page-label="表示件数" row-key="id"
    @row-click.stop="popMenu.open" @row-dblclick.stop="handleDoubleClick" />

  <div v-if="popMenu.show">
    <pop-menu :style="popMenu.style" />
  </div>
</template>

<script>
import { DateTime } from 'luxon';
import { useQuasar } from 'quasar';
import { useMypageStore } from 'src/store/mypage';
import { usePopMenuStore } from 'src/store/popMenu';
import {
  defineComponent, computed, ref, watch,
} from 'vue';
import { useRouter } from 'vue-router';
import PopMenu from './PopMenu.vue';

const postTypes = {
  addon_post: 'アドオン投稿',
  addon_introduction: 'アドオン紹介',
  markdown: 'markdown記事',
  page: '一般記事',
};
const statuses = {
  trashed: 'ゴミ箱',
  private: '非公開',
  draft: '下書き',
  reservation: '予約投稿',
  publish: '公開',
};
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
    field: (row) => statuses[row.status],
    label: 'ステータス',
    sortable: true,
    align: 'left',
    desc: '記事の公開状態',
  },
  {
    name: 'post_type',
    field: (row) => postTypes[row.post_type],
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
    name: 'totalRetweetCount',
    field: (row) => row.metrics.totalRetweetCount,
    label: 'RT',
    sortable: true,
    desc: '自動ツイートの合計RT数',
  },
  {
    name: 'totalReplyCount',
    field: (row) => row.metrics.totalReplyCount,
    label: 'Rep',
    sortable: true,
    desc: '自動ツイートの合計返信数',
  },
  {
    name: 'totalLikeCount',
    field: (row) => row.metrics.totalLikeCount,
    label: 'Like',
    sortable: true,
    desc: '自動ツイートの合計いいね数',
  },
  {
    name: 'totalQuoteCount',
    field: (row) => row.metrics.totalQuoteCount,
    label: 'QRT',
    sortable: true,
    desc: '自動ツイートの合計引用リツーイト数',
  },
  {
    name: 'totalImpressionCount',
    field: (row) => row.metrics.totalImpressionCount,
    label: 'IC',
    sortable: true,
    desc: '自動ツイートの合計表示回数',
  },
  {
    name: 'totalUrlLinkClicks',
    field: (row) => row.metrics.totalUrlLinkClicks,
    label: 'LC',
    sortable: true,
    desc: '自動ツイートのURL合計クリック数',
  },
  {
    name: 'totalUserProfileClicks',
    field: (row) => row.metrics.totalUserProfileClicks,
    label: 'UC',
    sortable: true,
    desc: '自動ツイートのプロフィール合計クリック数',
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
    const visibleColumns = ref($q.localStorage.getItem('mypage.visibleColumns')
      || ['id', 'status', 'post_type', 'title', 'totalViewCount', 'totalConversionCount', 'published_at', 'modified_at']);
    watch(visibleColumns, (val) => {
      $q.localStorage.set('mypage.visibleColumns', val);
    });
    const popMenu = usePopMenuStore();
    const router = useRouter();
    return {
      rows,
      columns,
      pagination,
      visibleColumns,
      options,
      popMenu,
      handleDoubleClick: (event, row) => {
        popMenu.close();
        router.push({ name: 'edit', params: { id: row.id } });
      },
    };
  },
  components: { PopMenu },
});
</script>
