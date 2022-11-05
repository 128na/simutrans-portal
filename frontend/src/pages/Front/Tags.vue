<template>
  <q-page class="q-pa-md">
    <text-title>タグ一覧</text-title>
    <div class="q-gutter-md">
      <q-btn v-for="t in tags" :key="t.id" :to="{ name: 'tag', params: { id: t.id } }" :size="size(t.count)" no-caps>
        {{ t.name }} ({{ t.count }})
      </q-btn>
    </div>
  </q-page>
</template>
<script>
import {
  defineComponent, ref,
} from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';
import { useApiHandler } from 'src/composables/apiHandler';

export default defineComponent({
  name: 'FrontTags',
  components: {
    TextTitle,
  },
  setup() {
    const { setTitle } = useMeta();
    setTitle('タグ一覧');

    const tags = ref([]);

    const api = useFrontApi();
    const handler = useApiHandler();
    const fetch = async () => {
      try {
        await handler.handleWithLoading({
          doRequest: api.fetchTags,
          done: (res) => {
            tags.value = res.data.data;
          },
          failedMessage: 'タグ一覧取得に失敗しました',
        });
      } catch {
        // do nothing.
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
      tags,
      size,
    };
  },
});
</script>
