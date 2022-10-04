<template>
  <q-list>
    <q-item v-show="loading">
      <q-item-section>
        <LoadingMessage />
      </q-item-section>
    </q-item>
    <q-item v-show="errorMessage">
      <q-item-section>
        <ApiErrorMessage :message="errorMessage" @retry="fetch" />
      </q-item-section>
    </q-item>
    <q-expansion-item v-for="(pakAddons, label) in pakAddonCounts" expand-separator :label="label" :key="label">
      <q-item v-for="(a, index) in pakAddons" clickable
        :to="{ name: 'categoryPak', params:{size:a.pak_slug, slug:a.addon_slug} }" :key="index">
        <q-item-section right>{{a.addon}} ({{a.count}})</q-item-section>
      </q-item>

    </q-expansion-item>
    <q-expansion-item v-show="!loading" expand-separator label="ユーザー一覧">
      <q-item v-for="(a, index) in userAddonCounts" clickable :to="{ name: 'user', params:{id:a.user_id} }"
        :key="index">
        <q-item-section>{{a.name}} ({{a.count}})</q-item-section>
      </q-item>
    </q-expansion-item>
    <q-item clickable :to="{ name: 'tags' }">
      <q-item-section>タグ一覧</q-item-section>
    </q-item>
    <q-separator />
    <q-item clickable :to="{ name: 'mypage' }">
      <q-item-section>マイページ</q-item-section>
    </q-item>
    <q-separator />
    <q-item clickable dense :to="{ name: 'about' }">
      <q-item-section>サイトの使い方</q-item-section>
    </q-item>
    <q-item clickable dense :to="{ name: 'privacy' }">
      <q-item-section>プライバシーポリシー</q-item-section>
    </q-item>
    <q-separator />
    <MetaLinks />
    <q-separator />
    <MetaInfo />
  </q-list>

</template>

<script>
import { defineComponent, ref } from 'vue';
import { api } from '../../boot/axios';
import MetaLinks from '../MetaLinks.vue';
import MetaInfo from '../MetaInfo.vue';
import LoadingMessage from '../Common/LoadingMessage.vue';
import ApiErrorMessage from '../Common/ApiErrorMessage.vue';

export default defineComponent({
  name: 'FrontMenu',
  setup() {
    const pakAddonCounts = ref({});
    const userAddonCounts = ref([]);
    const loading = ref(true);
    const errorMessage = ref(null);

    const fetch = async () => {
      loading.value = true;
      errorMessage.value = null;

      try {
        const res = await api.get('/api/v3/front/sidebar');
        if (res.status === 200) {
          pakAddonCounts.value = res.data.pakAddonCounts;
          userAddonCounts.value = res.data.userAddonCounts;
        }
      } catch (error) {
        errorMessage.value = 'メニューの取得に失敗しました';
      } finally {
        loading.value = false;
      }
    };
    fetch();

    return {
      pakAddonCounts,
      userAddonCounts,
      loading,
      errorMessage,
      fetch,
    };
  },
  components: {
    MetaLinks, MetaInfo, LoadingMessage, ApiErrorMessage,
  },
});
</script>
<style lang="scss">
.q-tree__node--selected .q-tree__node-header-content {
  color: $dark;
  font-weight: bold;
}
</style>
