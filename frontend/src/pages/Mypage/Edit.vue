<template>
  <q-page v-if="editor.ready && mypage.ready">
    <q-splitter v-model="editor.split" reverse :limits="[0, Infinity]" :style="style" ref="splitterRef"
      before-class="q-pa-md">
      <template v-slot:before>
        <div class="q-gutter-sm">
          <text-title>編集</text-title>
          <api-error-message :message="errorMessage" />
          <article-form />
          <form-without-update-modified-at />
          <form-tweet />
          <div class="row">
            <q-btn color="primary" @click="handle">保存する</q-btn>
            <q-space />
            <q-btn @click="editor.togglePreview()" color="secondary">
              {{ editor.split ? "プレビュー非表示" : "プレビュー表示" }}
              <q-icon name="keyboard_double_arrow_right" />
            </q-btn>
          </div>
        </div>
      </template>
      <template v-slot:after v-if="editor.split">
        <front-article-show :article="articleWithAttachments" class="q-px-md" />
      </template>
    </q-splitter>
  </q-page>
  <loading-page v-else />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { useAuthStore } from 'src/store/auth';
import {
  defineComponent, computed, ref, watchEffect, watch,
} from 'vue';
import { useRoute, useRouter } from 'vue-router';
import LoadingPage from 'src/components/Common/LoadingPage.vue';
import FrontArticleShow from 'src/components/Front/FrontArticleShow.vue';
import { useMypageStore } from 'src/store/mypage';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useQuasar, dom } from 'quasar';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';
import ArticleForm from 'src/components/Mypage/ArticleForm.vue';
import FormTweet from 'src/components/Mypage/FormTweet.vue';
import FormWithoutUpdateModifiedAt from 'src/components/Mypage/FormWithoutUpdateModifiedAt.vue';
import TextTitle from 'src/components/Common/TextTitle.vue';

export default defineComponent({
  name: 'MypageEdit',
  components: {
    ArticleForm,
    LoadingPage,
    FrontArticleShow,
    ApiErrorMessage,
    FormTweet,
    FormWithoutUpdateModifiedAt,
    TextTitle,
  },
  setup() {
    const mypage = useMypageStore();
    const editor = useArticleEditStore();
    const auth = useAuthStore();
    if (auth.validateAuth()) {
      if (!editor.optionsReady) {
        editor.fetchOptions();
      }
      if (!mypage.articlesReady) {
        mypage.fetchArticles();
      }
      if (!mypage.attachmentsReady) {
        mypage.fetchAttachments();
      }
    }

    const route = useRoute();
    const router = useRouter();
    const createArticle = () => {
      if (route.name !== 'edit') {
        return;
      } if (mypage.articlesReady) {
        const article = mypage.findArticleById(Number(route.params.id));
        if (article) {
          editor.setArticle(article);
        } else {
          router.push({ name: 'error', params: { status: 404 }, replace: true });
        }
      }
    };

    watch(mypage, () => {
      createArticle();
    }, { deep: true, immediate: true });
    watch(route, () => {
      createArticle();
    }, { deep: true, immediate: true });

    const articleWithAttachments = computed(() => ({
      ...editor.article,
      attachments: mypage.attachments,
      user: auth.user,
    }));

    const $q = useQuasar();
    const { errorMessage, errorHandlerStrict } = useErrorHandler();
    const handle = async () => {
      $q.loading.show();
      try {
        const articles = await editor.updateArticle();
        mypage.articles = articles;
      } catch (error) {
        errorHandlerStrict(error, '保存に失敗しました');
      } finally {
        $q.loading.hide();
      }
    };

    const splitterRef = ref(null);
    const style = ref({ height: '100vh' });
    watchEffect(() => {
      const el = splitterRef.value?.$el;
      if (el) {
        const { top } = dom.offset(el);
        style.value = { height: `calc(100vh - ${top}px)` };
      }
    }, { flush: 'post' });

    return {
      editor,
      mypage,
      articleWithAttachments,
      handle,
      errorMessage,
      splitterRef,
      style,
    };
  },
});
</script>
