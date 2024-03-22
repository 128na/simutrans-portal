<template>
  <label-optional>サムネイル画像</label-optional>
  <q-input :model-value="filename" readonly bottom-slots :error-message="editor.vali('article.contents.thumbnail')"
    :error="!!editor.vali('article.contents.thumbnail')">
    <template v-slot:after>
      <q-icon name="close" class="cursor-pointer q-mr-sm" @click="editor.article.contents.thumbnail = null" />
      <file-manager v-model="editor.article.contents.thumbnail" onlyImage attachmentableType="Article"
        :attachmentableId="editor.article.id" />
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
  name: 'FormThumbnail',
  components: { LabelOptional, FileManager },
  setup() {
    const editor = useArticleEditStore();
    const mypage = useMypageStore();
    const filename = computed(() => {
      if (!editor.article.contents.thumbnail) {
        return '未選択';
      }
      const file = mypage.findAttachmentById(editor.article.contents.thumbnail);

      return file?.original_name || 'ファイルが見つかりません';
    });
    return {
      editor,
      filename,
    };
  },
});
</script>
