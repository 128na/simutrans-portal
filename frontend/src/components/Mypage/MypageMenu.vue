<template>
  <q-list>
    <q-item href="/">
      <q-item-section>トップページ</q-item-section>
    </q-item>
    <q-item v-if="store.isAdmin" :to="{ name: 'admin' }">
      <q-item-section>管理トップ</q-item-section>
    </q-item>
    <q-separator />
    <template v-if="store.isVerified">
      <q-item>
        <q-item-section>記事</q-item-section>
      </q-item>
      <q-item :to="{ name: 'mypage' }" exact>
        <q-item-section avatar>
          <q-icon name="home" />
        </q-item-section>
        <q-item-section>記事一覧</q-item-section>
      </q-item>

      <q-item :to="{ name: 'create', params: { post_type: 'addon-post' } }">
        <q-item-section avatar>
          <q-icon name="article" />
        </q-item-section>
        <q-item-section>アドオン投稿</q-item-section>
      </q-item>
      <q-item :to="{ name: 'create', params: { post_type: 'addon-introduction' } }">
        <q-item-section avatar>
          <q-icon name="article" />
        </q-item-section>
        <q-item-section>アドオン紹介</q-item-section>
      </q-item>
      <q-item :to="{ name: 'create', params: { post_type: 'page' } }">
        <q-item-section avatar>
          <q-icon name="article" />
        </q-item-section>
        <q-item-section>一般記事</q-item-section>
      </q-item>
      <q-item :to="{ name: 'create', params: { post_type: 'markdown' } }">
        <q-item-section avatar>
          <q-icon name="article" />
        </q-item-section>
        <q-item-section>一般記事(markdown)</q-item-section>
      </q-item>
      <q-item :to="{ name: 'analytics' }">
        <q-item-section avatar>
          <q-icon name="timeline" />
        </q-item-section>
        <q-item-section>アクセス解析</q-item-section>
      </q-item>

      <q-item>
        <q-item-section>ユーザー</q-item-section>
      </q-item>

      <q-item :to="{ name: 'profile' }">
        <q-item-section avatar>
          <q-icon name="person" />
        </q-item-section>
        <q-item-section>プロフィール編集</q-item-section>
      </q-item>
      <q-item :to="{ name: 'twoFactorAuthentication' }">
        <q-item-section avatar>
          <q-icon name="lock" />
        </q-item-section>
        <q-item-section>2要素認証</q-item-section>
      </q-item>
      <q-item :to="{ name: 'invitation' }">
        <q-item-section avatar>
          <q-icon name="person_add" />
        </q-item-section>
        <q-item-section>ユーザー招待</q-item-section>
      </q-item>
      <q-item :to="{ name: 'loginHistory' }">
        <q-item-section avatar>
          <q-icon name="history" />
        </q-item-section>
        <q-item-section>ログイン履歴</q-item-section>
      </q-item>
    </template>
    <template v-if="store.isLoggedIn">
      <q-item :to="{ name: 'logout' }">
        <q-item-section avatar>
          <q-icon name="logout" />
        </q-item-section>
        <q-item-section>ログアウト</q-item-section>
      </q-item>
    </template>
    <q-item v-else :to="{ name: 'login' }">
      <q-item-section avatar>
        <q-icon name="login" />
      </q-item-section>
      <q-item-section>ログイン</q-item-section>
    </q-item>
    <q-separator />
    <MetaInfo />
  </q-list>
</template>

<script>
import { useAuthStore } from 'src/store/auth';
import { defineComponent } from 'vue';
import MetaInfo from 'src/components/Common/MetaInfo.vue';

export default defineComponent({
  name: 'MypageMenu',

  components: {
    MetaInfo,
  },

  setup() {
    const store = useAuthStore();
    return {
      store,
    };
  },
});
</script>
<style scoped>
.pad {
  padding-left: 72px;
}
</style>
