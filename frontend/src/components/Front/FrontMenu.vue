<template>
  <q-list>
    <q-item>
      <search-form />
    </q-item>
    <q-separator />
    <q-item clickable href="/mypage">
      <q-item-section>マイページ</q-item-section>
    </q-item>
    <q-separator />
    <q-item v-show="handler.loading.value">
      <q-item-section>
        <LoadingMessage />
      </q-item-section>
    </q-item>
    <q-expansion-item v-for="(pakAddons, label) in pakAddonCounts" expand-separator :label="label" :key="label">
      <q-item v-for="(a, index) in pakAddons" clickable
        :to="{ name: 'categoryPak', params: { size: a.pak_slug, slug: a.addon_slug } }" :key="index">
        <q-item-section right>{{ a.addon }} ({{ a.count }})</q-item-section>
      </q-item>

    </q-expansion-item>
    <q-expansion-item v-show="!handler.loading.value" expand-separator label="ユーザー一覧">
      <q-item v-for="(a, index) in userAddonCounts" clickable
        :to="{ name: 'user', params: { idOrNickname: a.user_nickname || a.user_id } }" :key="index">
        <q-item-section>{{ a.user_name }} ({{ a.count }})</q-item-section>
      </q-item>
    </q-expansion-item>
    <q-item clickable :to="{ name: 'tags' }">
      <q-item-section>タグ一覧</q-item-section>
    </q-item>
    <q-separator />
    <q-item clickable dense :href="SUPPORT_SITE_URL" target="_blank">
      <q-item-section>サイトの使い方</q-item-section>
    </q-item>
    <q-item clickable dense :href="PRIVACY_POLICY_URL" target="_blank">
      <q-item-section>プライバシーポリシー</q-item-section>
    </q-item>
    <q-separator />
    <q-item clickable dense :to="{ name: 'social' }">
      <q-item-section>SNS・通知ツール</q-item-section>
    </q-item>
  </q-list>
</template>

<script>
import { defineComponent, ref } from 'vue';
import { useFrontApi } from 'src/composables/api';
import LoadingMessage from 'src/components/Common/Text/LoadingMessage.vue';
import SearchForm from 'src/components/Front/SearchForm.vue';
import { useApiHandler } from 'src/composables/apiHandler';
import { SUPPORT_SITE_URL, PRIVACY_POLICY_URL } from 'src/const';

export default defineComponent({
  name: 'FrontMenu',
  components: {
    LoadingMessage,
    SearchForm,
  },
  setup() {
    const pakAddonCounts = ref({});
    const userAddonCounts = ref([]);

    const handler = useApiHandler();
    const api = useFrontApi();
    const fetch = async () => {
      try {
        await handler.handle({
          doRequest: () => api.fetchSidebar(),
          done: (res) => {
            pakAddonCounts.value = res.data.pakAddonCounts;
            userAddonCounts.value = res.data.userAddonCounts;
          },
          failedMessage: 'メニューの取得に失敗しました',
        });
      } catch {
        // do nothing
      }
    };
    fetch();

    return {
      pakAddonCounts,
      userAddonCounts,
      handler,
      SUPPORT_SITE_URL,
      PRIVACY_POLICY_URL,
    };
  },
});
</script>
<style lang="scss">
.q-tree__node--selected .q-tree__node-header-content {
  color: $dark;
  font-weight: bold;
}
</style>
