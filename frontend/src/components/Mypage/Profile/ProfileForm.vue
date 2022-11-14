<template>
  <q-input label-slot v-model="editor.user.name">
    <template v-slot:label>
      <label-required>名前</label-required>
    </template>
  </q-input>
  <q-input label-slot v-model="editor.user.email">
    <template v-slot:label>
      <label-required>メールアドレス</label-required>
    </template>
  </q-input>
  <small>
    メールアドレスを変更すると新しいメールアドレスへ確認メールが送られます。
  </small>
  <form-avatar />
  <input-countable label-slot v-model="editor.user.profile.data.description" :maxLength="1024">
    <label-required>説明</label-required>
  </input-countable>
  <q-input label-slot v-model="editor.user.profile.data.website">
    <template v-slot:label>
      <label-optional>WebサイトURL</label-optional>
    </template>
  </q-input>
  <q-input label-slot v-model="editor.user.profile.data.twitter">
    <template v-slot:label>
      <label-optional>Twitter ID</label-optional>
    </template>
  </q-input>
  <q-input label-slot v-model="editor.user.profile.data.gtag">
    <template v-slot:label>
      <label-optional>Google Tag Manager連携</label-optional>
    </template>
  </q-input>
</template>
<script>
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import LabelOptional from 'src/components/Common/LabelOptional.vue';
import FormAvatar from 'src/components/Mypage/Profile/FormAvatar.vue';
import { defineComponent } from 'vue';
import { useProfileEditStore } from 'src/store/profileEdit';
import InputCountable from 'src/components/Common/Input/InputCountable.vue';

export default defineComponent({
  name: 'ProfileForm',
  components: {
    InputCountable,
    LabelRequired,
    LabelOptional,
    FormAvatar,
  },
  setup() {
    const editor = useProfileEditStore();

    return {
      editor,
    };
  },
});
</script>
