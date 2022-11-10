<template>
  <q-list>
    <q-item>
      <search-form />
    </q-item>
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
      <q-item v-for="(a, index) in userAddonCounts" clickable :to="{ name: 'user', params: { id: a.user_id } }"
        :key="index">
        <q-item-section>{{ a.name }} ({{ a.count }})</q-item-section>
      </q-item>
    </q-expansion-item>
    <q-item clickable :to="{ name: 'tags' }">
      <q-item-section>タグ一覧</q-item-section>
    </q-item>
    <q-separator />
    <q-item clickable href="/mypage">
      <q-item-section>マイページ</q-item-section>
    </q-item>
    <q-separator />
    <q-item clickable dense :to="{ name: 'show', params: { slug: 'about' } }">
      <q-item-section>サイトの使い方</q-item-section>
    </q-item>
    <q-item clickable dense :to="{ name: 'show', params: { slug: 'privacy' } }">
      <q-item-section>プライバシーポリシー</q-item-section>
    </q-item>
    <q-separator />
    <meta-links />
    <q-separator />
    <MetaInfo />
  </q-list>

</template>

<script>
import { defineComponent, ref } from 'vue';
import { useFrontApi } from 'src/composables/api';
import MetaLinks from 'src/components/Common/MetaLinks.vue';
import MetaInfo from 'src/components/Common/MetaInfo.vue';
import LoadingMessage from 'src/components/Common/Text/LoadingMessage.vue';
import SearchForm from 'src/components/Front/SearchForm.vue';
import { useApiHandler } from 'src/composables/apiHandler';

export default defineComponent({
  name: 'FrontMenu',
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
    };
  },
  components: {
    MetaLinks,
    MetaInfo,
    LoadingMessage,
    SearchForm,
  },
});
</script>
<style lang="scss">
.q-tree__node--selected .q-tree__node-header-content {
  color: $dark;
  font-weight: bold;
}
</style>
