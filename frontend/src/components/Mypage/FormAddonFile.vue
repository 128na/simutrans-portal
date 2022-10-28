<template>
  <label-optional>アドオンファイル</label-optional>
  <q-input :model-value="filename" readonly>
    <template v-slot:append>
      <q-icon name="close" class="cursor-pointer q-mr-sm" @click="editor.article.contents.file  =  null" />
      <file-manager v-model="editor.article.contents.file" />
    </template>
  </q-input>
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import LabelOptional from 'src/components/Common/LabelOptional.vue';
import { defineComponent, computed } from 'vue';
import { useMypageStore } from 'src/store/mypage';
import FileManager from 'src/components/Mypage/FileManager.vue';

export default defineComponent({
  name: 'FormAddonFile',
  components: { LabelOptional, FileManager },
  setup() {
    const editor = useArticleEditStore();
    const mypage = useMypageStore();
    const filename = computed(() => {
      if (!editor.article.contents.file) {
        return '未選択';
      }
      const file = mypage.findAttachmentById(editor.article.contents.file);

      return file?.original_name || 'ファイルが見つかりません';
    });
    return {
      editor,
      filename,
    };
  },
});
</script>
