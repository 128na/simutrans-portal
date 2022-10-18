<template>
  <q-page v-if="editor.ready">
    <div class="row">
      <div class="col q-pa-md q-gutter-md">
        <article-form />
        <div class="row">
          <q-btn color="primary">保存する</q-btn>
          <q-space />
          <q-btn @click="editor.togglePreview()" color="secondary">
            {{editor.preview ? "プレビュー非表示" : "プレビュー表示"}}
            <q-icon name="keyboard_double_arrow_right" />
          </q-btn>
        </div>
      </div>
      <div class="col q-pa-md" v-show="editor.preview">
        <front-article-show :article="articleWithAttachments" />
      </div>
    </div>
  </q-page>
  <loading-page v-else />
</template>
<script>
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';
import { useArticleEditStore } from 'src/store/articleEdit';
import { useAuthStore } from 'src/store/auth';
import { defineComponent, computed } from 'vue';
import { useRoute, useRouter, onBeforeRouteUpdate } from 'vue-router';
import LoadingPage from 'src/components/Common/LoadingPage.vue';
import FrontArticleShow from 'src/components/Front/FrontArticleShow.vue';
import { useMypageStore } from 'src/store/mypage';
import ArticleForm from '../../components/Mypage/ArticleForm.vue';

export default defineComponent({
  name: 'MypageCreate',
  components: { ArticleForm, LoadingPage, FrontArticleShow },
  setup() {
    const editor = useArticleEditStore();
    const route = useRoute();
    const router = useRouter();
    const createArticle = (currentRoute) => {
      switch (currentRoute.params.post_type) {
        case 'addon_introduction':
          return editor.createAddonIntroduction();
        case 'addon_post':
          return editor.createAddonPost();
        case 'page':
          return editor.createPage();
        case 'markdown':
          return editor.createMarkdown();
        default:
          return router.push({ name: 'error', params: { status: 404 } });
      }
    };
    const api = useMypageApi();
    const notify = useNotify();
    const fetchOptions = async () => {
      if (editor.options) {
        return;
      }
      try {
        const res = await api.fetchOptions();
        editor.options = res.data;
      } catch (error) {
        notify.failed('カテゴリ一覧取得に失敗しました');
        notify.failedRetryable('カテゴリ一覧取得に失敗しました', fetchOptions);
      }
    };

    const mypage = useMypageStore();
    const fetchAttachments = async () => {
      if (mypage.attachments) {
        return;
      }
      try {
        const res = await api.fetchAttachments();
        mypage.attachments = res.data.data;
      } catch (error) {
        notify.failedRetryable('添付ファイル一覧取得に失敗しました', fetchAttachments);
      }
    };
    const auth = useAuthStore();
    if (auth.validateAuth()) {
      fetchOptions();
      fetchAttachments();
    }

    createArticle(route);
    onBeforeRouteUpdate((to) => {
      createArticle(to);
    });

    const articleWithAttachments = computed(() => Object.assign(
      editor.article,
      { attachments: mypage.attachments },
      { user: auth.user },
    ));

    return {
      editor,
      articleWithAttachments,
    };
  },
});
</script>
