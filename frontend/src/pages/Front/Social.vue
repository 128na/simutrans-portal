<template>
  <q-page class="q-pa-md">
    <TextTitle>SNS・通知ツール</TextTitle>
    <p>記事の更新を各種ツールで受け取れます。</p>
    <TextSubTitle>プッシュ通知</TextSubTitle>
    <p>※登録解除はブラウザ設定から権限設定から可能です。</p>
    <q-btn color="primary" @click=handleOneSign>
      登録する
    </q-btn>
    <TextSubTitle>Twitter</TextSubTitle>
    <p>記事が投稿・更新されると自動でツイートされます。</p>
    <a href="https://twitter.com/PortalSimutrans" target="_blank" rel="noopener nofollow"
      class="text-primary">@PortalSimutrans</a>

    <TextSubTitle>Misskey</TextSubTitle>
    <p>記事が投稿・更新されると自動でツイートされます。</p>
    <a href="https://misskey.io/@PortalSimutrans" target="_blank" rel="noopener nofollow"
      class="text-primary">@PortalSimutrans</a>

    <TextSubTitle>Bluesky</TextSubTitle>
    <p>記事が投稿・更新されると自動でツイートされます。</p>
    <a href="https://bsky.app/profile/portalsimutrans.bsky.social" target="_blank" rel="noopener nofollow"
      class="text-primary">@PortalSimutrans.bsky.social</a>

    <TextSubTitle>RSS</TextSubTitle>
    <p>RSSフィーダーやSlackなど各種ツールと連携させると新着情報が入手できます。</p>
    <q-list bordered separator class="rounded-borders">
      <q-item href="/feed" target="_blank">
        <q-item-section>
          <q-item-label>アドオン新着</q-item-label>
          <q-item-label caption>全てのアドオン新着順</q-item-label>
        </q-item-section>
      </q-item>
      <q-item href="/feed/pak128-japan" target="_blank">
        <q-item-section>
          <q-item-label>Pak128Japanアドオン新着</q-item-label>
          <q-item-label caption>Pak128Japanカテゴリのアドオン新着順</q-item-label>
        </q-item-section>
      </q-item>
      <q-item href="/feed/pak128" target="_blank">
        <q-item-section>
          <q-item-label>Pak128アドオン新着</q-item-label>
          <q-item-label caption>Pak128カテゴリのアドオン新着順</q-item-label>
        </q-item-section>
      </q-item>
      <q-item href="/feed/pak64" target="_blank">
        <q-item-section>
          <q-item-label>Pak64アドオン新着</q-item-label>
          <q-item-label caption>Pak64カテゴリのアドオン新着順</q-item-label>
        </q-item-section>
      </q-item>
      <q-item href="/feed/page" target="_blank">
        <q-item-section>
          <q-item-label>一般記事</q-item-label>
          <q-item-label caption>一般記事新着順</q-item-label>
        </q-item-section>
      </q-item>
      <q-item href="/feed/announce" target="_blank">
        <q-item-section>
          <q-item-label>お知らせ</q-item-label>
          <q-item-label caption>お知らせ新着順</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>

    <TextSubTitle>横断検索 Discord Bot(試験運用中)</TextSubTitle>
    <p>Discordサーバーに追加すると、「!cs 検索したいキーワード」でアドオン検索できます。</p>
    <q-list bordered separator class="rounded-borders">
      <q-item href="https://discord.com/oauth2/authorize?client_id=1076747667411054612&scope=bot&permissions=0"
        target="_blank">
        <q-item-section>
          <q-item-label>Botを追加する</q-item-label>
          <q-item-label caption>サーバーに追加後、Botに反応させたいチャンネルの「チャンネルを見る」、「メッセージを送信」の権限付与が必要です。</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
    <TextSubTitle>横断検索 LINE Bot(試験運用中)</TextSubTitle>
    <p>QRコードから友達登録すると、アドオン検索できます。</p>
    <q-img src="https://qr-official.line.me/sid/L/866zkymz.png" width="160px" />

    <TextSubTitle>その他開発情報など</TextSubTitle>
    <q-list bordered separator class="rounded-borders">
      <q-item href="https://twitter.com/128Na" target="_blank" rel="noopener nofollow">
        <q-item-section>
          <q-item-label>@128Na</q-item-label>
          <q-item-label caption>サイトに関するお問い合わせはこちらまで</q-item-label>
        </q-item-section>
      </q-item>
      <q-item href="https://github.com/128na/simutrans-portal" target="_blank" rel="noopener nofollow">
        <q-item-section>
          <q-item-label>Github</q-item-label>
          <q-item-label caption>自力で開発・不具合修正できる人はこちらにどうぞ</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </q-page>
</template>

<script>
import { defineComponent, onMounted } from 'vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useMeta } from 'src/composables/meta';
import TextSubTitle from 'src/components/Common/Text/TextSubTitle.vue';
import { useQuasar } from 'quasar';

const registerTwitterWidget = () => {
  const script = document.createElement('script');
  script.setAttribute('src', 'https://platform.twitter.com/widgets.js');
  script.async = true;
  document.head.appendChild(script);
};

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
      registerTwitterWidget();
    });

    const handleOneSign = async () => {
      const supported = window.OneSignal.isPushNotificationsSupported();
      if (!supported) {
        // eslint-disable-next-line no-alert
        window.alert('このデバイスはプッシュ通知未対応です');
        return;
      }

      // 既に通知許可済みならフラグ切替のみ
      const enabled = await window.OneSignal.isPushNotificationsEnabled();
      if (enabled) {
        // eslint-disable-next-line no-alert
        window.alert('登録済みです。');
        return;
      }

      window.OneSignal.showNativePrompt();
    };

    return {
      mode: $q.dark.isActive ? 'dark' : 'light',
      handleOneSign,
    };
  },
});
</script>
