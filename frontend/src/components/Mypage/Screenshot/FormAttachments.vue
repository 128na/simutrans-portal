<template>
  <draggable v-model="editor.screenshot.attachments" item-key="id" class="row q-gutter-sm">
    <template #item="{ element, index }">
      <div>
        <a :href="mypage.findAttachmentById(element.id)?.url" target="_blank" rel="noreferrer noopener">
          <q-img :src="mypage.findAttachmentById(element.id)?.url" width="256px" />
        </a>
        <q-input v-model="editor.screenshot.attachments[index].caption" label-slot bottom-slots
          :error-message="editor.vali(`screenshot.attachments.${index}.caption`)"
          :error="!!editor.vali(`screenshot.attachments.${index}.caption`)">
          <template v-slot:label>
            <label-optional>キャプション</label-optional>
          </template>
        </q-input>
      </div>
    </template>
  </draggable>

  <div class="q-my-md">
    <FileManager v-model="attachmentIds" :onlyImage="true" attachmentableType="Screenshot"
      :attachmentableId="editor.screenshot.id" />
  </div>
</template>
<script>
import { defineComponent, computed, watchEffect } from 'vue';
import { useScreenshotEditStore } from 'src/store/screenshotEdit';
import { useMypageStore } from 'src/store/mypage';
import LabelOptional from 'src/components/Common/LabelOptional.vue';
import FileManager from 'src/components/Mypage/FileManager.vue';
import draggable from 'vuedraggable';

export default defineComponent({
  name: 'FormAttachments',
  components: {
    FileManager,
    LabelOptional,
    draggable,
  },
  props: {
  },
  setup() {
    const mypage = useMypageStore();
    const editor = useScreenshotEditStore();
    const attachmentIds = computed({
      get() {
        return editor.screenshot.attachments.map((a) => a.id);
      },
      set(ids) {
        editor.screenshot.attachments = ids.map((id, index) => {
          const attachment = mypage.findAttachmentById(id);
          return { ...attachment, caption: editor.screenshot.attachments[index]?.caption ?? '' };
        });
      },
    });
    watchEffect(() => {
      // 表示順をオーダーに適用
      editor.screenshot.attachments.map((a, index) => {
        a.order = index + 1;
        return a;
      });
    });

    return {
      mypage,
      editor,
      attachmentIds,
    };
  },
});
</script>
