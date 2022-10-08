<template>
  <q-page>
    <q-item v-show="loading">
      <q-item-section>
        <loading-message />
      </q-item-section>
    </q-item>
    <q-item v-show="error">
      <q-item-section>
        <api-error-message message="記事取得に失敗しました" @retry="fetchArticle" />
      </q-item-section>
    </q-item>
    <q-item v-if="article">
      <front-article-show :article="article" />
    </q-item>
  </q-page>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import FrontArticleShow from 'src/components/Front/FrontArticleShow.vue';
import { api } from '../../boot/axios';
import { metaHandler } from '../../composables/metaHandler';
import LoadingMessage from '../../components/Common/LoadingMessage.vue';
import ApiErrorMessage from '../../components/Common/ApiErrorMessage.vue';

export default defineComponent({
  name: 'FrontShow',
  components: {
    LoadingMessage,
    ApiErrorMessage,
    FrontArticleShow,
  },

  props: {
    cachedArticles: {
      type: Array,
      default: () => [],
    },
  },

  setup(props, { emit }) {
    const loading = ref(true);
    const error = ref(false);

    const route = useRoute();
    api.post(`/api/v3/shown/${route.params.slug}`);

    const article = computed(() => props.cachedArticles.find((a) => a.slug === route.params.slug));

    const fetchArticle = async () => {
      const { setTitle } = metaHandler();
      if (props.cachedArticles.find((a) => a.slug === route.params.slug)) {
        loading.value = false;
        setTitle(article.value.title);
        return;
      }

      loading.value = true;
      error.value = false;
      try {
        const res = await api.get(`/api/v3/front/articles/${route.params.slug}`);
        if (res.status === 200) {
          emit('addCache', res.data.data);
          setTitle(res.data.data.title);
        }
      } catch (err) {
        error.value = true;
      } finally {
        loading.value = false;
      }
    };
    fetchArticle();

    return {
      article,
      loading,
      error,
      fetchArticle,
    };
  },
});
</script>
