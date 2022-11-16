<template>
  <q-page class="q-ma-md">
    <text-title>機能制限</text-title>

    <div v-for="c in controllOptions" :key="c.key">
      <q-toggle :model-value="c.value" color="green" :label="c.key" @update:model-value="toggle(c.key)" />
    </div>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useAdminApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';
import { useAuthStore } from 'src/store/auth';
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'PageAdminControllOptions',
  components: { TextTitle },
  setup() {
    const auth = useAuthStore();
    auth.validateAuth();

    const meta = useMeta();
    meta.setTitle('機能制限');

    const controllOptions = ref([]);
    const api = useAdminApi();
    const fetch = async () => {
      const res = await api.fetchControllOptions();
      controllOptions.value = res.data;
    };
    fetch();
    const toggle = async (key) => {
      const res = await api.toggleControllOption(key);
      controllOptions.value = res.data;
    };
    return {
      controllOptions,
      toggle,
    };
  },
});
</script>
