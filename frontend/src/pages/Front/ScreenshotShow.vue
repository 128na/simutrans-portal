<template>
  <q-page class="q-ma-md" v-if="screenshot">
    <TextTitle>
      {{ screenshot.title }}
    </TextTitle>
    <dl>
      <dt>説明</dt>
      <dd>
        <TextPre>{{ screenshot.description }}</TextPre>
      </dd>
      <dt>投稿者</dt>
      <dd>{{ screenshot.user.name }}</dd>
      <RelatedArticles v-if="screenshot.articles.length" :articles="screenshot.articles" />
      <RelatedLinks v-if="screenshot.links.length" :links="screenshot.links" />
      <dt>公開日時</dt>
      <dd>{{ screenshot.published_at }}</dd>
    </dl>
    <template v-for="a in screenshot.attachments" :key="a.id">
      <figure>
        <a :href="a.url" target="_blank" rel="noreferrer noopener">
          <img :src="a.url" style="max-width:100%;height: auto" loading="lazy" />
        </a>
        <figcaption v-if="a.caption">{{ a.caption }}</figcaption>
      </figure>
    </template>
  </q-page>
</template>

<script>
import RelatedArticles from 'src/components/Common/Screenshot/RelatedArticles.vue';
import RelatedLinks from 'src/components/Common/Screenshot/RelatedLinks.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import TextPre from 'src/components/Common/Text/TextPre.vue';
import { useFrontApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';
import { useMeta } from 'src/composables/meta';
import { useScreenshotCache } from 'src/store/screenshotCache';
import { defineComponent, computed, watch } from 'vue';
import { useRoute } from 'vue-router';

export default defineComponent({
  name: 'ScreenshotShow',
  components: {
    TextTitle,
    TextPre,
    RelatedArticles,
    RelatedLinks,
  },

  setup() {
    const cache = useScreenshotCache();
    const route = useRoute();
    const screenshot = computed(() => cache.getCache(route.params.id));

    const { setTitle } = useMeta();

    const api = useFrontApi();
    const handler = useApiHandler();
    const fetch = async () => {
      if (route.name !== 'screenshotShow') {
        return;
      }
      if (screenshot.value) {
        setTitle(screenshot.value.title);
        return;
      }
      try {
        await handler.handleWithLoading({
          doRequest: () => api.fetchScreenshot(route.params.id),
          done: (res) => {
            cache.addCache(res.data.data);
            setTitle(res.data.data.title);
          },
          failedMessage: '取得に失敗しました',
        });
      } catch {
        // do nothing
      }
    };
    watch(route, () => { fetch(); }, { deep: true, immediate: true });

    return {
      screenshot,
    };
  },
});
</script>
