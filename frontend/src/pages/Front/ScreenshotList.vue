<template>
  <q-page>
    <q-list>
      <q-item>
        <div class="row q-gutter-md">
          <template v-for="s in screenshots" :key="s.id">
            <router-link :to="{ name: 'screenshotShow', params: { id: s.id } }">
              <ScreenshotThumbnail :screenshot="s" />
            </router-link>
          </template>
        </div>
      </q-item>

      <q-item v-if="pagination" class="flex flex-center">
        <q-pagination :model-value="pagination.current_page" :min="1" :max="pagination.last_page" :max-pages="3"
          :to-fn="handlePagination" direction-links boundary-links />
      </q-item>
    </q-list>
  </q-page>
</template>

<script>
import { useFrontApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';
import { useMeta } from 'src/composables/meta';
import { useScreenshotCache } from 'src/store/screenshotCache';
import { defineComponent, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import ScreenshotThumbnail from 'src/components/Common/Screenshot/ScreenshotThumbnail.vue';

export default defineComponent({
  name: 'ScreenshotList',
  components: {
    ScreenshotThumbnail,
  },

  setup() {
    const screenshots = ref([]);
    const pagination = ref(null);

    const { setTitle } = useMeta();
    setTitle('スクリーンショット一覧');

    const cache = useScreenshotCache();
    const api = useFrontApi();
    const handler = useApiHandler();

    const route = useRoute();
    const fetch = async () => {
      try {
        await handler.handleWithLoading({
          doRequest: () => api.fetchScreenshots(route.query.page || 1),
          done: (res) => {
            screenshots.value = res.data.data;
            pagination.value = res.data.meta;
            cache.addCaches(res.data.data);
          },
          failedMessage: '取得に失敗しました',
        });
      } catch {
        // do nothing
      }
    };
    watch(route, () => { fetch(); }, { deep: true, immediate: true });

    const handlePagination = (page) => ({
      query: { ...route.query, page },
    });
    return {
      pagination,
      screenshots,
      handlePagination,
    };
  },
});
</script>
