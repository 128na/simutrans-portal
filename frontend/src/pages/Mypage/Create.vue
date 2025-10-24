<template>
  <q-page v-if="editor.ready">
    <div class="q-gutter-sm q-ma-md">
      <text-title>新規作成</text-title>
      <article-form />
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
import FormNotify from 'src/components/Mypage/ArticleForm/FormNotify.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import ArticleForm from 'src/components/Mypage/PostType/ArticleForm.vue';
import { useMeta } from 'src/composables/meta';

export default defineComponent({
  name: 'MypageCreate',
  components: {
    ArticleForm,
    LoadingPage,
    FormNotify,
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

    const meta = useMeta();
    meta.setTitle('新規作成');

    const route = useRoute();
    const router = useRouter();
    const createArticle = () => {
      if (route.name !== 'create') {
        return;
      }
      try {
        editor.createArticle(route.params.post_type);
      } catch (error) {
        router.push({ name: 'error', params: { status: 404 }, replace: true });
      }
    };
    watch(route, () => createArticle(), { immediate: true, deep: true });

    const articleWithAttachments = computed(() => ({
      ...editor.article,
      attachments: mypage.attachments,
      user: auth.user,
    }));

    const handle = async () => {
      const { slug } = editor.article;
      const articles = await editor.saveArticle();
      mypage.articles = articles;
      const article = mypage.findArticleBySlug(slug);
      if (article) {
        router.push({ name: 'edit', params: { id: article.id } });
      }
    };

    return {
      editor,
      articleWithAttachments,
      handle,
    };
  },
});
</script>
