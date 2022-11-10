<template>
  <q-page class="q-ma-md">
    <div class="q-gutter-sm">
      <text-title>ユーザー招待</text-title>
      <div>
        このサイトへユーザーを招待、登録できるようにします。<br>
        ユーザーを招待にするには招待URLを生成し、招待したいユーザーにURLを伝えてください。<br>
      </div>
      <text-sub-title>注意事項</text-sub-title>
      <div>
        <ul>
          <li>招待URLは招待者のみに共有してください</li>
          <li>SNSなどに誰でも見れる場所への招待URLの公開は禁止です</li>
          <li>
            URLを再生成、削除すると以前の招待URLからはユーザー登録できなくなります
          </li>
          <li>
            招待されたユーザーに何らかの問題がある場合、招待したユーザーのアカウントが制限されることがあります
          </li>
        </ul>
      </div>
      <q-input :model-value="auth.user.invitation_url || '未生成'" readonly label="招待URL" />
      <template v-if="auth.user.invitation_url">
        <q-btn color="secondary" @click="handleCopy" label="URLコピー" />
        <q-btn color="primary" @click="handleUpdate" label="再生成" />
        <q-btn color="negative" @click="handleDelete" label="削除" />
      </template>
      <template v-else>
        <q-btn color="primary" @click="handleUpdate" label="生成" />
      </template>
      <text-sub-title>招待したユーザー</text-sub-title>

      <div v-if="invitation.hasInvites">
        <ul>
          <li v-for="invite in invitation.invites" :key="invite.id">
            {{ invite.id }}. {{ invite.name }} ({{ invite.created_at }})
          </li>
        </ul>
      </div>
      <div v-else>
        招待したユーザーはいません。
      </div>
    </div>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import TextSubTitle from 'src/components/Common/Text/TextSubTitle.vue';
import { useAuthStore } from 'src/store/auth';
import { useInvitationStore } from 'src/store/invitation';
import { defineComponent } from 'vue';
import { useClipboard } from 'src/composables/clipboard';
import { useNotify } from 'src/composables/notify';
import { useMeta } from 'src/composables/meta';

export default defineComponent({
  name: 'PageInvitation',
  components: {
    TextTitle, TextSubTitle,
  },
  setup() {
    const auth = useAuthStore();
    const invitation = useInvitationStore();
    if (auth.validateAuth()) {
      invitation.fetch();
    }
    const meta = useMeta();
    meta.setTitle('ユーザー招待');

    const handleUpdate = async () => {
      try {
        const user = await invitation.regenerate();
        auth.setUser(user);
      } catch {
        // do nothing
      }
    };
    const handleDelete = async () => {
      try {
        const user = await invitation.revoke();
        auth.setUser(user);
      } catch {
        // do nothing
      }
    };

    const clipboad = useClipboard();
    const notify = useNotify();
    const handleCopy = async () => {
      try {
        clipboad.write(auth.user.invitation_url);
        notify.success('コピーしました');
      } catch (err) {
        notify.failed('コピーに失敗しました');
      }
    };

    return {
      auth,
      invitation,
      handleUpdate,
      handleDelete,
      handleCopy,
    };
  },
});
</script>
