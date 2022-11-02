<template>
  <label-optional>アバター画像</label-optional>
  <q-input :model-value="filename" readonly>
    <template v-slot:append>
      <q-icon name="close" class="cursor-pointer q-mr-sm" @click="editor.user.profile.data.avatar = null" />
      <file-manager v-model="editor.user.profile.data.avatar" onlyImage attachmentableType="Profile"
        :attachmentableId="editor.user.profile.id" />
    </template>
  </q-input>
</template>
<script>
import LabelOptional from 'src/components/Common/LabelOptional.vue';
import { defineComponent, computed } from 'vue';
import { useMypageStore } from 'src/store/mypage';
import FileManager from 'src/components/Mypage/FileManager.vue';
import { useProfileEditStore } from 'src/store/profileEdit';

export default defineComponent({
  name: 'FormAvatar',
  components: { LabelOptional, FileManager },
  setup() {
    const editor = useProfileEditStore();
    const mypage = useMypageStore();
    const filename = computed(() => {
      if (!editor.user.profile.data.avatar) {
        return '未選択';
      }
      const file = mypage.findAttachmentById(editor.user.profile.data.avatar);

      return file?.original_name || 'ファイルが見つかりません';
    });
    return {
      editor,
      filename,
    };
  },
});
</script>
