<template>
  <q-page class="q-pa-md">
    <text-title>ログアウト中…</text-title>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { defineComponent } from 'vue';
import { useAuthStore } from 'src/store/auth';

export default defineComponent({
  name: 'PageLogout',
  components: {
    TextTitle,
  },
  setup() {
    const store = useAuthStore();
    store.attemptLogout();
  },
});
</script>
