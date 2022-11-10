<template>
  <q-page></q-page>
</template>
<script>
import { useMeta } from 'src/composables/meta';
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'PageAdminTop',
  components: {},
  setup() {
    const auth = useAuthStore();
    auth.validateAuth();

    const meta = useMeta();
    meta.setTitle('管理トップ');
  },
});
</script>
