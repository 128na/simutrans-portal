<template>
  <text-title>リダイレクト一覧</text-title>
  <div>
    記事URL変更で設定されたリダイレクト設定の確認、削除ができます。<br>
    ※リダイレクト設定がループすると記事が表示されなくなるためご注意ください。<br>
    例：/example1 → /example2 と /example1 → /example1 を同時に設定
  </div>
  <ul>
    <li v-for="r in redirects" :key="r.id" class="q-mb-sm">
      <div class="word-break">{{ r.from }}→{{ r.to }}</div>
      <q-btn color="negative" size="sm" @click="destroy(r)">削除</q-btn>
    </li>
  </ul>
</template>
<script>
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { defineComponent, ref } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';

export default defineComponent({
  name: 'RedirectList',
  components: {
    TextTitle,
  },
  setup() {
    const api = useMypageApi();
    const { handle, handleWithLoading } = useApiHandler();
    const redirects = ref([]);

    handle({
      doRequest: () => api.fetchRedirects(),
      done: (res) => {
        redirects.value = res.data.data || [];
      },
    });
    const destroy = async (redirect) => {
      if (window.confirm('この設定を削除しますか？')) {
        handleWithLoading({
          doRequest: () => api.deleteRedirect(redirect.id),
          done: (res) => {
            redirects.value = res.data.data || [];
          },
        });
      }
    };

    return {
      redirects,
      destroy,
    };
  },
});
</script>
