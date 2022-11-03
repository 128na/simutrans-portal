<template>
  <q-list>
    <q-item :to="{ name: 'top' }">
      <q-item-section>トップページ</q-item-section>
    </q-item>
    <q-item :to="{ name: 'mypage' }">
      <q-item-section>マイページ</q-item-section>
    </q-item>
    <q-item :to="{ name: 'admin' }" exact>
      <q-item-section>管理トップ</q-item-section>
    </q-item>
    <q-separator />
    <q-item :to="{ name: 'admin.token' }">
      <q-item-section avatar>
        <q-icon name="key" />
      </q-item-section>
      <q-item-section>トークン</q-item-section>
    </q-item>
    <q-item :to="{ name: 'admin.articles' }">
      <q-item-section avatar>
        <q-icon name="article" />
      </q-item-section>
      <q-item-section>記事</q-item-section>
    </q-item>
    <q-item :to="{ name: 'admin.users' }">
      <q-item-section avatar>
        <q-icon name="people" />
      </q-item-section>
      <q-item-section>ユーザー</q-item-section>
    </q-item>
    <q-separator />
    <MetaInfo />
  </q-list>

</template>

<script>
import { defineComponent } from 'vue';
import MetaInfo from '../Common/MetaInfo.vue';

export default defineComponent({
  name: 'AdminMenu',

  components: {
    MetaInfo,
  },

  setup() {
    return {
    };
  },
});
</script>
