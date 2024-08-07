<template>
  <label-optional>関連記事（10件まで）</label-optional>
  <template v-for="(article, index) in modelValue" :key="index">
    <div>
      {{ article.title }}
      <q-btn icon="close" round outline color="negative" size="xs" @click="remove(index)" />
      <slot name="validate" :index="index" />
    </div>
  </template>
  <q-btn color="primary" outline label="記事を追加" class="q-my-sm" @click="showDialog" />
  <q-dialog v-model="show" maximized>
    <q-layout view="hHh lpR fFf">
      <q-header elevated class="bg-dark text-white">
        <q-toolbar>
          <q-toolbar-title>記事選択</q-toolbar-title>
          <q-space />
          <q-btn dense flat icon="close" v-close-popup />
        </q-toolbar>
      </q-header>
      <q-page-container class="bg-white">
        <q-page class="q-ma-md">
          <q-btn-toggle v-model="onlyMyArticle" toggle-color="primary" :options="modes" />

          <q-table :rows="suggestArticles" :columns="columns" :rows-per-page-options="[20, 50, 100, 0]" title="記事一覧"
            rows-per-page-label="表示件数" no-results-label="該当記事がありません"
            :no-data-label="onlyMyArticle ? '記事がありません' : '検索ワードを入力してください'" row-key="id" @row-click.stop="handleClick">
            <template v-slot:top-right>
              <q-input borderless dense debounce="100" v-model="searchWord" placeholder="タイトル検索" clearable>
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </template>
          </q-table>
        </q-page>
      </q-page-container>
    </q-layout>
  </q-dialog>
</template>
<script>
import LabelOptional from 'src/components/Common/LabelOptional.vue';
import { useMypageStore } from 'src/store/mypage';
import {
  defineComponent, ref, computed, watchEffect,
} from 'vue';
import { DateTime } from 'luxon';
import { useFrontApi } from 'src/composables/api';

const MAX_RELATIONS = 10;
const modes = [{ label: '自分の公開記事', value: true }, { label: 'すべての公開記事', value: false }];

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
    name: 'title',
    field: 'title',
    label: 'タイトル',
    sortable: true,
    align: 'left',
    desc: '記事のタイトル',
  },
  {
    name: 'user',
    field: (row) => row?.user?.name || '-',
    label: '投稿者',
    sortable: true,
    align: 'left',
    desc: '投稿者',
  },
  {
    name: 'published_at',
    field: (row) => (row.published_at ? DateTime.fromISO(row.published_at).toLocaleString(DateTime.DATETIME_SHORT) : '-'),
    label: '投稿日時',
    sortable: true,
    align: 'left',
    desc: '記事の投稿（予約）日時',
  },
];
export default defineComponent({
  name: 'FormArticleRelations',
  components: {
    LabelOptional,
  },
  props: {
    modelValue: {
      type: Array,
      required: true,
    },
  },
  setup(props, { emit }) {
    const mypage = useMypageStore();
    const show = ref(false);
    const showDialog = () => {
      if (props.modelValue.length < MAX_RELATIONS) {
        show.value = true;
      }
    };
    const onlyMyArticle = ref(true);
    const searchWord = ref('');
    const searchArticles = ref([]);
    const suggestArticles = computed(() => {
      if (onlyMyArticle.value) {
        return mypage.articles.filter((a) => a.status === 'publish' && (searchWord.value ? a.title.includes(searchWord.value) : true));
      }
      return searchArticles.value;
    });
    const frontApi = useFrontApi();
    watchEffect(async () => {
      if (onlyMyArticle.value === false && searchWord.value) {
        const res = await frontApi.fetchSearch(searchWord.value);
        searchArticles.value = res.data.data;
      }
    });
    const handleClick = (event, row) => {
      emit('update:modelValue', [...props.modelValue, {
        id: row.id,
        title: row.title,
      }]);
      show.value = false;
    };

    const remove = (index) => {
      if (window.confirm('記事へのリンクを削除しますか？')) {
        emit('update:modelValue', [...props.modelValue.filter((a, i) => i !== index)]);
      }
    };
    return {
      remove,
      show,
      showDialog,
      columns,
      onlyMyArticle,
      modes,
      searchWord,
      suggestArticles,
      handleClick,
    };
  },
});
</script>
