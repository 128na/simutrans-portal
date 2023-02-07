<template>
  <q-page class="q-pa-md">
    <TextTitle>Discord招待リンク</TextTitle>
    <p>「シムトランス交流会議-~simutrans-interact-meeting~」の招待リンクを発行できます。</p>
    <p>※リンクは5分間1人まで有効です。</p>

    <p v-if="url">招待リンクを発行しました。<a :href=url>こちら</a>からアクセスして下さい。</p>
    <p v-else>
      <q-btn :loading="loading" :disable="disable" color="primary" label="発行する" @click="handle" />
    </p>

  </q-page>
</template>

<script>
import { defineComponent, ref } from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useFrontApi } from 'src/composables/api';
import { useMeta } from 'src/composables/meta';

export default defineComponent({
  name: 'DiscordInvite',
  components: {
    TextTitle,
  },

  setup() {
    const { setTitle } = useMeta();
    setTitle('Discord招待リンク');

    const api = useFrontApi();
    const loading = ref(false);
    const disable = ref(false);
    const url = ref(null);
    const errorMessage = ref(null);
    const handle = async () => {
      try {
        loading.value = true;
        await new Promise((resolve) => { window.grecaptcha.enterprise.ready(resolve); });
        const token = await window.grecaptcha.enterprise.execute(process.env.GOOGLE_RECAPTCHA_SITE_KEY, { action: 'invite' });

        const res = await api.discordInvite(token);

        url.value = res.data.url;
      } catch (error) {
        errorMessage.value = '現在利用できません';
      } finally {
        disable.value = true;
        loading.value = false;
      }
    };

    return {
      handle,
      loading,
      disable,
      url,
      errorMessage,
    };
  },
});
</script>
