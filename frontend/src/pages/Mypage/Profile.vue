<template>
  <q-page v-if="mypage.attachmentsReady && editor.ready" class="q-ma-md">
    <div class="q-gutter-sm">
      <text-title>プレビュー</text-title>
      <description-profile :description="previewData" />
      <text-title>プロフィール編集</text-title>
      <api-error-message :message="editor.handler.validationErrorMessage" />
      <profile-form />
      <q-btn color="primary" @click="handle">保存する</q-btn>
    </div>
  </q-page>
  <loading-page v-else />
</template>
<script>
import ProfileForm from 'src/components/Mypage/Profile/ProfileForm.vue';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import { useAuthStore } from 'src/store/auth';
import { DEFAULT_AVATAR } from 'src/const';
import { useMypageStore } from 'src/store/mypage';
import { useProfileEditStore } from 'src/store/profileEdit';
import { defineComponent, computed } from 'vue';
import LoadingPage from 'src/components/Common/LoadingPage.vue';
import ApiErrorMessage from 'src/components/Common/Text/ApiErrorMessage.vue';
import DescriptionProfile from 'src/components/Front/Description/DescriptionProfile.vue';
import { useRouter } from 'vue-router';
import { useMeta } from 'src/composables/meta';

export default defineComponent({
  name: 'PageProfile',
  components: {
    TextTitle,
    ProfileForm,
    LoadingPage,
    ApiErrorMessage,
    DescriptionProfile,
  },
  setup() {
    const auth = useAuthStore();
    const mypage = useMypageStore();
    const editor = useProfileEditStore();
    const router = useRouter();
    if (auth.validateAuth()) {
      if (!mypage.attachmentsReady) {
        mypage.fetchAttachments();
      }
      editor.setUser(auth.user);
    }
    const meta = useMeta();
    meta.setTitle('プロフィール編集');

    const handle = async () => {
      try {
        const user = await editor.updateUser();
        auth.setUser(user);
        router.push({ name: 'mypage' });
      } catch {
        // do nothing
      }
    };
    const avatarUrl = computed(() => {
      if (editor.user.profile.data.avatar) {
        const file = mypage.findAttachmentById(editor.user.profile.data.avatar);
        if (file) {
          return file.url;
        }
      }
      return DEFAULT_AVATAR;
    });
    const previewData = computed(() => ({
      profile: {
        avatar_url: avatarUrl.value,
        name: editor.user.name,
        description: editor.user.profile.data.description,
        website: editor.user.profile.data.website,
      },
    }));

    return {
      auth,
      mypage,
      editor,
      handle,
      previewData,
    };
  },
});
</script>
