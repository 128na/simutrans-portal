<template>
  <q-list>
    <q-item :to="{name:'top'}">
      <q-item-section>トップページ</q-item-section>
    </q-item>
    <q-item :to="{name:'admin'}">
      <q-item-section>管理画面</q-item-section>
    </q-item>
    <q-item v-if="store.isLoggedIn" clickable @click="logout">
      <q-item-section>ログアウト</q-item-section>
    </q-item>
    <q-item v-else :to="{name:'login'}">
      <q-item-section>ログイン</q-item-section>
    </q-item>
    <q-separator />
    <MetaInfo />
  </q-list>

</template>

<script>
import { useMypageApi } from 'src/composables/api';
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';
import { useRouter } from 'vue-router';
import MetaInfo from '../MetaInfo.vue';

export default defineComponent({
  name: 'MypageMenu',

  components: {
    MetaInfo,
  },

  setup() {
    const store = useAuthStore();
    const { postLogout } = useMypageApi();
    const router = useRouter();
    const logout = () => {
      postLogout();
      store.logout();
      router.push({ name: 'top' });
    };
    return {
      store,
      logout,
    };
  },
});
</script>
