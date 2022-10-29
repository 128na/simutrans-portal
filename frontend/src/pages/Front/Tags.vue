<template>
  <q-page class="q-pa-md">
    <text-title>タグ一覧</text-title>
    <loading-message v-show="loading" />
    <api-error-message v-show="error" :message="errorMessage" @retry="fetch" />
    <div class="q-gutter-md">
      <q-btn v-for="t in tags" :key="t.id" :to="{ name: 'tag', params: { id: t.id } }"
        :size="size(t.count)" no-caps>
        {{ t.name }} ({{ t.count }})
      </q-btn>
    </div>
  </q-page>
</template>
<script>
import {
  defineComponent, ref,
} from 'vue';
import LoadingMessage from 'src/components/Common/LoadingMessage.vue';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';
import TextTitle from 'src/components/Common/TextTitle.vue';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';

export default defineComponent({
  name: 'FrontTags',
  components: {
    LoadingMessage,
    ApiErrorMessage,
    TextTitle,
  },
  setup() {
    const { setTitle } = useMeta();
    setTitle('タグ一覧');

    const loading = ref(true);
    const error = ref(false);
    const tags = ref([]);

    const { errorMessage, errorHandlerStrict } = useErrorHandler();
    const { fetchTags } = useFrontApi();
    const fetch = async () => {
      loading.value = true;
      error.value = false;

      try {
        const res = await fetchTags();
        if (res.status === 200) {
          tags.value = res.data.data;
        }
      } catch (err) {
        error.value = true;
        errorHandlerStrict(err, 'タグ一覧取得に失敗しました');
      } finally {
        loading.value = false;
      }
    };
    fetch();

    const size = (count) => {
      if (count > 20) {
        return 'xl';
      }
      if (count > 10) {
        return 'lg';
      }
      return 'md';
    };

    return {
      loading,
      error,
      tags,
      fetch,
      size,
      errorMessage,
    };
  },
});
</script>
