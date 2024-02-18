<template>
  <text-title>ログイン履歴</text-title>
  <div>
    過去10回分のログイン履歴が確認できます。
  </div>
  <q-input :model-value="loginHistories || '履歴がありません'" type="textarea" readonly autogrow filled />
</template>
<script>
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { defineComponent, ref } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';
import { DateTime } from 'luxon';

export default defineComponent({
  name: 'LoginHistory',
  components: {
    TextTitle,
  },
  setup() {
    const api = useMypageApi();
    const { handle } = useApiHandler();
    const loginHistories = ref(null);
    const toDateTimeString = (date) => DateTime.fromISO(date).toLocaleString(DateTime.DATETIME_SHORT);

    handle({
      doRequest: () => api.fetchLoginHistories(),
      done: (res) => {
        loginHistories.value = (res.data || [])
          .map((r) => `${toDateTimeString(r.created_at)} IP: ${r.ip} ${r.ua}`)
          .join('\n');
      },
    });

    return {
      loginHistories,
      toDateTimeString,
    };
  },
});
</script>
