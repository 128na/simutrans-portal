<template>
  <q-page>
    <q-list v-if="screenshot">
      <q-item>
        <TextTitle>
          {{ screenshot.title }}
        </TextTitle>
      </q-item>
      <q-item>
        <TextPre>
          {{ screenshot.description }}
        </TextPre>
      </q-item>
      <q-item>
        投稿者：{{ screenshot.user.name }}
      </q-item>
      <q-item>
        投稿日時：{{ screenshot.updated_at }}
      </q-item>
      <q-item>
        関連記事
        <template v-for="a in screenshot.articles" :key="a.id">
          <div>{{ a.title }}</div>
        </template>
      </q-item>
      <q-item>
        関連リンク
        <template v-for="(l, i) in screenshot.links" :key="i">
          <div>{{ l }}</div>
        </template>
      </q-item>
      <template v-for="a in screenshot.attachments" :key="a.id">
        <q-item :href="a.url" target="_blank" rel="noreferrer noopener">
          <q-img :src="a.url" width="100%" />
        </q-item>
      </template>
    </q-list>
  </q-page>
</template>

<script>
import TextPre from 'src/components/Common/Text/TextPre.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
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
