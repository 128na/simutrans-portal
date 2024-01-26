<template>
  <q-input label-slot v-model="editor.user.name" bottom-slots :error-message="editor.vali('user.name')"
    :error="!!editor.vali('user.name')">
    <template v-slot:label>
      <label-required>名前</label-required>
    </template>
  </q-input>
  <q-input label-slot v-model="editor.user.email" bottom-slots :error-message="editor.vali('user.email')"
    :error="!!editor.vali('user.email')">
    <template v-slot:label>
      <label-required>メールアドレス</label-required>
    </template>
  </q-input>
  <small>
    メールアドレスを変更すると新しいメールアドレスへ確認メールが送られます。
  </small>
  <q-input label-slot v-model="editor.user.nickname" bottom-slots :error-message="editor.vali('user.nickname')"
    :error="!!editor.vali('user.nickname')">
    <template v-slot:label>
      <label-optional>ニックネーム</label-optional>
    </template>
  </q-input>
  <small>
    設定するとユーザー記事一覧や記事詳細のURLに使用されます。例: users/my-nickname/article123<br />
  </small>
  <form-avatar />
  <input-countable label-slot v-model="editor.user.profile.data.description" :maxLength="1024" bottom-slots
    :error-message="editor.vali('user.profile.data.description')"
    :error="!!editor.vali('user.profile.data.description')">
    <label-optional>説明</label-optional>
  </input-countable>
  <q-input label-slot v-model="editor.user.profile.data.website" bottom-slots
    :error-message="editor.vali('user.profile.data.website')" :error="!!editor.vali('user.profile.data.website')">
    <template v-slot:label>
      <label-optional>WebサイトURL</label-optional>
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
