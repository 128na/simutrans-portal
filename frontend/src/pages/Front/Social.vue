<template>
  <q-page class="q-pa-md">
    <TextTitle>SNS・通知ツール</TextTitle>
    <p>記事の更新を各種ツールで受け取れます。</p>
    <TextSubTitle>Twitterアカウント</TextSubTitle>
    <li>
      <a href="https://twitter.com/PortalSimutrans" target="_blank" rel="noopener nofollow"
        class="text-primary">@PortalSimutrans</a>
      <a class="twitter-timeline" data-width="480" data-height="360" :data-theme="mode"
        href="https://twitter.com/PortalSimutrans?ref_src=twsrc%5Etfw"></a>
    </li>
    <TextSubTitle>RSS</TextSubTitle>
    <li>
      <a href="/feed" class="text-primary">アドオン新着</a>
    </li>
    <li>
      <a href="/feed/pak128-japan" class="text-primary">Pak128Japanアドオン新着</a>
    </li>
    <li>
      <a href="/feed/pak128" class="text-primary">Pak128アドオン新着</a>
    </li>
    <li>
      <a href="/feed/pak64" class="text-primary">Pak64アドオン新着</a>
    </li>
    <li>
      <a href="/feed/pak64" class="text-primary">一般記事</a>
    </li>
    <li>
      <a href="/feed/pak64" class="text-primary">お知らせ</a>
    </li>
    <TextSubTitle>その他開発情報など</TextSubTitle>
    <li>
      <a href="https://twitter.com/128Na" target="_blank" rel="noopener nofollow" class="text-primary">@128Na</a>
    </li>
    <li>
      <a href="https://github.com/128na/simutrans-portal" target="_blank" rel="noopener nofollow"
        class="text-primary">Github</a>
    </li>
  </q-page>
</template>

<script>
import { defineComponent, onMounted } from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useMeta } from 'src/composables/meta';
import TextSubTitle from 'src/components/Common/Text/TextSubTitle.vue';
import { useQuasar } from 'quasar';

export default defineComponent({
  name: 'PageSocial',
  components: {
    TextTitle,
    TextSubTitle,
  },

  setup() {
    const { setTitle } = useMeta();
    setTitle('SNS・通知ツール');
    const $q = useQuasar();

    onMounted(() => {
      const script = document.createElement('script');
      script.setAttribute('src', 'https://platform.twitter.com/widgets.js');
      script.async = true;
      document.head.appendChild(script);
    });

    return {
      mode: $q.dark.isActive ? 'dark' : 'light',
    };
  },
});
</script>
