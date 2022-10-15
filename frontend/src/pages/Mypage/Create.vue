<template>
  <q-page>
    example
  </q-page>
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';
import { useRoute, useRouter, onBeforeRouteUpdate } from 'vue-router';

export default defineComponent({
  name: 'MypageCreate',
  components: {},
  setup() {
    const store = useArticleEditStore();
    const route = useRoute();
    const router = useRouter();
    const createArticle = (currentRoute) => {
      switch (currentRoute.params.post_type) {
        case 'addon_introduction':
          return store.createAddonIntroduction();
        case 'addon_post':
          return store.createAddonPost();
        case 'page':
          return store.createPage();
        case 'markdown':
          return store.createMarkdown();
        default:
          return router.push({ name: 'error', params: { status: 404 } });
      }
    };

    const auth = useAuthStore();
    if (auth.validateAuth(route)) {
      createArticle(route);
      onBeforeRouteUpdate((to) => {
        createArticle(to);
      });
    }
  },
});
</script>
