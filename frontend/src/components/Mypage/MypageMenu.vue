<template>
  <q-list>
    <q-item :to="{name:'top'}">
      <q-item-section>トップページ</q-item-section>
    </q-item>
    <template v-if="store.isLoggedIn">
      <q-item :to="{name:'mypage'}">
        <q-item-section>マイページ</q-item-section>
      </q-item>
      <q-item clickable @click="logout">
        <q-item-section>ログアウト</q-item-section>
      </q-item>
    </template>
    <q-item v-else :to="{name:'login'}">
      <q-item-section>ログイン</q-item-section>
    </q-item>
    <q-item v-if="store.isAdmin" :to="{name:'admin'}">
      <q-item-section>管理トップ</q-item-section>
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
import MetaInfo from 'src/components/Common/MetaInfo.vue';
import { useNotify } from 'src/composables/notify';

export default defineComponent({
  name: 'MypageMenu',

  components: {
    MetaInfo,
  },

  setup() {
    const store = useAuthStore();
    const { postLogout } = useMypageApi();
    const router = useRouter();
    const notify = useNotify();
    const logout = () => {
      postLogout();
      store.logout();
      notify.info('ログアウトしました');
      router.push({ name: 'login' });
    };
    return {
      store,
      logout,
    };
  },
});
</script>
