<template>
  <q-page v-if="editor.ready">
    <q-splitter v-model="editor.split" reverse :limits="[0, Infinity]" :style="style" ref="splitterRef"
      before-class="q-pa-md">
      <template v-slot:before>
        <div class="q-gutter-sm">
          <text-title>新規作成</text-title>
          <article-form />
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
import { dom } from 'quasar';
import FormTweet from 'src/components/Mypage/ArticleForm/FormTweet.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import ArticleForm from 'src/components/Mypage/PostType/ArticleForm.vue';
import { useMeta } from 'src/composables/meta';

export default defineComponent({
  name: 'MypageCreate',
  components: {
    ArticleForm,
    LoadingPage,
    FrontArticleShow,
    FormTweet,
    TextTitle,
  },
  setup() {
    const mypage = useMypageStore();
    const editor = useArticleEditStore();
    const auth = useAuthStore();
    if (auth.validateAuth()) {
      editor.fetchOptions();
      mypage.fetchAttachments();
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

    const splitterRef = ref(null);
    const style = ref({ height: '100vh' });
    watchEffect(() => {
      const el = splitterRef.value?.$el;
      if (el) {
        const { top } = dom.offset(el);
        style.value = { height: `calc(100vh - ${top}px)` };
      }
    }, { flush: 'post' });

    const handle = async () => {
      try {
        const { slug } = editor.article;
        const articles = await editor.saveArticle();
        mypage.articles = articles;
        const article = mypage.findArticleBySlug(slug);
        if (article) {
          router.push({ name: 'edit', params: { id: article.id } });
        }
      } catch {
        const el = splitterRef.value.$el.querySelector('.q-splitter__before');
        el.scrollTo({ top: 0, behavior: 'smooth' });
      }
    };

    return {
      editor,
      articleWithAttachments,
      handle,
      splitterRef,
      style,
    };
  },
});
</script>
