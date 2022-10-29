<template>
  <q-card>
    <q-toolbar class="bg-primary text-white">
      <div>{{ row.id }}. {{ row.title }}</div>
      <q-space />
      <q-btn flat round dense icon="close" v-close-popup />
    </q-toolbar>
    <q-list>
      <q-item :to="{ name: 'edit', params: { id: row.id } }">
        <q-item-section avatar>
          <q-icon name="edit" />
        </q-item-section>
        <q-item-section>
          編集
        </q-item-section>
      </q-item>
      <q-item v-if="row.status === 'publish'" :to="{ name: 'show', params: { slug: row.slug } }">
        <q-item-section avatar>
          <q-icon name="launch" />
        </q-item-section>
        <q-item-section>記事を表示</q-item-section>
      </q-item>
      <q-item v-if="row.status === 'publish'" clickable @click="copy">
        <q-item-section avatar>
          <q-icon name="content_paste" />
        </q-item-section>
        <q-item-section>URLをコピー</q-item-section>
      </q-item>
      <q-item v-if="row.status === 'publish'" clickable @click="handleToPrivate">
        <q-item-section avatar>
          <q-icon name="lock" />
        </q-item-section>
        <q-item-section>
          記事を非公開にする
        </q-item-section>
      </q-item>
      <q-item v-else clickable @click="handleToPublish">
        <q-item-section avatar>
          <q-icon name="lock_open" />
        </q-item-section>
        <q-item-section>
          記事を公開にする（自動ツイート無し）
        </q-item-section>
      </q-item>
    </q-list>
  </q-card>
</template>
<script>
import { useNotify } from 'src/composables/notify';
import { useClipboard } from 'src/composables/clipboard';
import {
  defineComponent,
} from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useMypageStore } from 'src/store/mypage';

export default defineComponent({
  name: 'DialogMenu',
  props: {
    row: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const notify = useNotify();
    const clipboad = useClipboard();
    const copy = async () => {
      try {
        clipboad.write(`${window.location.origin}/articles/${props.row.slug}`);
        notify.success('コピーしました');
      } catch (err) {
        notify.failed('コピーに失敗しました');
      }
    };

    const api = useMypageApi();
    const store = useMypageStore();
    const handleToPrivate = async () => {
      const article = store.findArticleById(props.row.id);
      if (!article) {
        return notify.failed('記事が見つかりませんでした');
      }
      try {
        const res = await api.updateArticle({ article: Object.assign(article, { status: 'private' }) });
        if (res.status === 200 && res.data.data) {
          store.articles = res.data.data;
          return notify.success('記事を非公開に変更しました');
        }
      } catch (error) {
        // do nothing
      }
      return notify.failed('更新に失敗しました');
    };
    const handleToPublish = async () => {
      const article = store.findArticleById(props.row.id);
      if (!article) {
        return notify.failed('記事が見つかりませんでした');
      }
      try {
        const res = await api.updateArticle({ article: Object.assign(article, { status: 'publish' }) });
        if (res.status === 200 && res.data.data) {
          store.articles = res.data.data;
          return notify.success('記事を公開に変更しました');
        }
      } catch (error) {
        // do nothing
      }
      return notify.failed('更新に失敗しました');
    };

    return {
      copy,
      handleToPrivate,
      handleToPublish,
    };
  },
});
</script>
