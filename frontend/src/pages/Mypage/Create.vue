<template>
  <q-page v-if="editor.ready">
    <q-splitter v-model="editor.split" reverse :limits="[0, Infinity]" :style="style" ref="splitterRef"
      before-class="q-pa-md q-gutter-sm">
      <template v-slot:before>
        <api-error-message :message="errorMessage" />
        <article-form />
        <div class="row">
          <q-btn color="primary" @click="handle">保存する</q-btn>
          <q-space />
          <q-btn @click="editor.togglePreview()" color="secondary">
            {{editor.split ? "プレビュー非表示" : "プレビュー表示"}}
            <q-icon name="keyboard_double_arrow_right" />
          </q-btn>
        </div>
      </template>
      <template v-slot:after v-if="editor.split">
        <front-article-show :article="articleWithAttachments" class="q-pa-md" />
      </template>

    </q-splitter>
  </q-page>
  <loading-page v-else />
</template>
<script>
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';
import { useArticleEditStore } from 'src/store/articleEdit';
import { useAuthStore } from 'src/store/auth';
import {
  defineComponent, computed, ref, watchEffect,
} from 'vue';
import { useRoute, useRouter, onBeforeRouteUpdate } from 'vue-router';
import LoadingPage from 'src/components/Common/LoadingPage.vue';
import FrontArticleShow from 'src/components/Front/FrontArticleShow.vue';
import { useMypageStore } from 'src/store/mypage';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useQuasar, dom } from 'quasar';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';
import ArticleForm from '../../components/Mypage/ArticleForm.vue';

export default defineComponent({
  name: 'MypageCreate',
  components: {
    ArticleForm, LoadingPage, FrontArticleShow, ApiErrorMessage,
  },
  setup() {
    const splitterRef = ref(null);
    const editor = useArticleEditStore();
    const route = useRoute();
    const router = useRouter();
    const createArticle = (currentRoute) => {
      switch (currentRoute.params.post_type) {
        case 'addon-introduction':
          return editor.createAddonIntroduction();
        case 'addon-post':
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

    const $q = useQuasar();
    const { errorMessage, errorHandlerStrict } = useErrorHandler();
    const handle = async () => {
      $q.loading.show();
      try {
        const params = {
          article: editor.article,
          should_tweet: editor.tweet,
        };
        const res = await api.createArticle(params);
        notify.success('保存しました');
        router.push({ name: 'edit', params: { id: res.data.data.id } });
      } catch (error) {
        errorHandlerStrict(error, '保存に失敗しました');
      } finally {
        $q.loading.hide();
      }
    };

    const style = ref({ height: '100vh' });
    watchEffect(() => {
      const val = splitterRef.value;
      if (val) {
        const { top } = dom.offset(val.$el);
        style.value = { height: `calc(100vh - ${top}px)` };
      }
    }, { flush: 'post' });

    return {
      splitterRef,
      editor,
      articleWithAttachments,
      handle,
      errorMessage,
      style,
    };
  },
});
</script>
