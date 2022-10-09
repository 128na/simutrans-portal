<template>
  <q-list>
    <q-item>
      <search-form />
    </q-item>
    <q-item v-show="loading">
      <q-item-section>
        <LoadingMessage />
      </q-item-section>
    </q-item>
    <q-item v-show="error">
      <q-item-section>
        <api-error-message :message="errorMessage" @retry="fetch" />
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
    <q-item clickable dense :to="{ name:'show',params:{slug: 'about'} }">
      <q-item-section>サイトの使い方</q-item-section>
    </q-item>
    <q-item clickable dense :to="{ name:'show',params:{slug: 'privacy'} }">
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
import { useRouter } from 'vue-router';
import { useErrorHandler } from 'src/composables/errorHandler';
import { useFrontApi } from 'src/composables/api';
import MetaLinks from 'src/components/Common/MetaLinks.vue';
import MetaInfo from 'src/components/Common/MetaInfo.vue';
import LoadingMessage from 'src/components/Common/LoadingMessage.vue';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';
import SearchForm from 'src/components/Front/SearchForm.vue';

export default defineComponent({
  name: 'FrontMenu',
  setup() {
    const pakAddonCounts = ref({});
    const userAddonCounts = ref([]);
    const loading = ref(true);
    const error = ref(false);

    const { errorMessage, errorHandler } = useErrorHandler(useRouter());

    const { fetchSidebar } = useFrontApi();
    const fetch = async () => {
      loading.value = true;
      error.value = false;

      try {
        const res = await fetchSidebar();
        if (res.status === 200) {
          pakAddonCounts.value = res.data.pakAddonCounts;
          userAddonCounts.value = res.data.userAddonCounts;
        }
      } catch (err) {
        error.value = true;
        errorHandler(err, 'メニューの取得に失敗しました');
      } finally {
        loading.value = false;
      }
    };
    fetch();

    return {
      pakAddonCounts,
      userAddonCounts,
      loading,
      error,
      errorMessage,
      fetch,
    };
  },
  components: {
    MetaLinks,
    MetaInfo,
    LoadingMessage,
    ApiErrorMessage,
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
