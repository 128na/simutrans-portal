<template>
  <q-page v-if="mypage.attachmentsReady && editor.ready" class="q-ma-md">
    <div class="q-gutter-sm">
      <text-title>プロフィール編集</text-title>
      <api-error-message :message="editor.errorMessage" />
      <profile-form />
      <q-btn color="primary" @click="handle">保存する</q-btn>
    </div>
  </q-page>
  <loading-page v-else />
</template>
<script>
import ProfileForm from 'src/components/Mypage/ProfileForm.vue';
import TextTitle from 'src/components/Common/TextTitle.vue';
import { useAuthStore } from 'src/store/auth';
import { useMypageStore } from 'src/store/mypage';
import { useProfileEditStore } from 'src/store/profileEdit';
import { defineComponent } from 'vue';
import LoadingPage from 'src/components/Common/LoadingPage.vue';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';
import { useRouter } from 'vue-router';

export default defineComponent({
  name: 'PageProfile',
  components: {
    TextTitle,
    ProfileForm,
    LoadingPage,
    ApiErrorMessage,
  },
  setup() {
    const auth = useAuthStore();
    const mypage = useMypageStore();
    const editor = useProfileEditStore();
    const router = useRouter();
    if (auth.validateAuth()) {
      mypage.fetchAttachments();
      editor.setUser(auth.user);
    }
    const handle = async () => {
      const user = await editor.updateUser();
      auth.setUser(user);
      router.push({ name: 'mypage' });
    };

    return {
      auth,
      mypage,
      editor,
      handle,
    };
  },
});
</script>
