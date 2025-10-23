<template>
  <q-page v-if="editor.ready && mypage.ready">
    <div class="q-gutter-sm q-ma-md">
      <text-title>編集</text-title>
      <article-form />
      <form-without-update-modified-at />
      <form-notify />
      <div class="row">
        <q-btn color="primary" @click="handle">保存する</q-btn>
      </div>
    </div>
  </q-page>
  <loading-page v-else />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { useAuthStore } from 'src/store/auth';
import {
  defineComponent, computed, watch,
} from 'vue';
import { useRoute, useRouter } from 'vue-router';
import LoadingPage from 'src/components/Common/LoadingPage.vue';
import { useMypageStore } from 'src/store/mypage';
import ArticleForm from 'src/components/Mypage/PostType/ArticleForm.vue';
import FormNotify from 'src/components/Mypage/ArticleForm/FormNotify.vue';
import FormWithoutUpdateModifiedAt from 'src/components/Mypage/ArticleForm/FormWithoutUpdateModifiedAt.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useMeta } from 'src/composables/meta';

export default defineComponent({
  name: 'MypageEdit',
  components: {
    ArticleForm,
    LoadingPage,
    FormNotify,
    FormWithoutUpdateModifiedAt,
    TextTitle,
  },
  setup() {
    const mypage = useMypageStore();
    const editor = useArticleEditStore();
    editor.clearArticle();
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

    const meta = useMeta();
    meta.setTitle('編集');

    const route = useRoute();
    const router = useRouter();
    const createArticle = () => {
      if (route.name !== 'edit') {
        editor.clearArticle();
        return;
      }
      if (mypage.articlesReady) {
        const article = mypage.findArticleById(Number(route.params.id));
        if (article) {
          editor.setArticle(article);
        } else {
          router.push({ name: 'error', params: { status: 404 }, replace: true });
        }
      }
    };

    watch(mypage, () => {
      if (!editor.articleInitialized) {
        createArticle();
      }
    }, { deep: true, immediate: true });
    watch(route, () => {
      if (!editor.articleInitialized) {
        createArticle();
      }
    }, { deep: true, immediate: true });

    const articleWithAttachments = computed(() => ({
      ...editor.article,
      attachments: mypage.attachments,
      user: auth.user,
    }));

    const handle = async () => {
      const articles = await editor.updateArticle();
      mypage.articles = articles;
    };

    return {
      editor,
      mypage,
      articleWithAttachments,
      handle,
    };
  },
});
</script>
