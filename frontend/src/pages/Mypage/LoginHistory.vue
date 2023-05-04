<template>
  <q-page class="q-ma-md">
    <div class="q-gutter-sm">
      <text-title>ログイン履歴</text-title>
      <div>
        過去10回分のログイン履歴が確認できます。
      </div>

      <ul>
        <template v-if="loginHistories.length">
          <li v-for="history in loginHistories" :key="history.id">
            {{ toDateTimeString(history.created_at) }} IP: {{ history.ip }}
            <br>{{ history.ua }}
          </li>
        </template>
        <li v-else>
          履歴がありません
        </li>
      </ul>
    </div>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useAuthStore } from 'src/store/auth';
import { defineComponent, ref } from 'vue';
import { useMeta } from 'src/composables/meta';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';
import { DateTime } from 'luxon';

export default defineComponent({
  name: 'PageInvitation',
  components: {
    TextTitle,
  },
  setup() {
    const auth = useAuthStore();
    const api = useMypageApi();
    const { handle } = useApiHandler();
    const loginHistories = ref([]);
    if (auth.validateAuth()) {
      handle({
        doRequest: () => api.fetchLoginHistories(),
        done: (res) => { loginHistories.value = res.data || []; },
      });
    }
    const meta = useMeta();
    meta.setTitle('ログイン履歴');

    const toDateTimeString = (date) => DateTime.fromISO(date).toLocaleString(DateTime.DATETIME_SHORT);

    return {
      auth,
      loginHistories,
      toDateTimeString,
    };
  },
});
</script>
