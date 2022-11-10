<template>
  <q-page></q-page>
</template>
<script>
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'PageAdminTop',
  components: {},
  setup() {
    const auth = useAuthStore();
    auth.validateAuth();
  },
});
</script>
