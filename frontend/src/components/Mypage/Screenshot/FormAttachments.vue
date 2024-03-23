<template>
  <div class="row q-gutter-sm">
    <template v-for="(attachment, index) in  editor.screenshot.attachments " :key="attachment.id">
      <div>
        <a :href="mypage.findAttachmentById(attachment.id)?.url" target="_blank" rel="noreferrer noopener">
          <q-img :src="mypage.findAttachmentById(attachment.id)?.url" width="256px" />
        </a>
        <q-input v-model="editor.screenshot.attachments[index].caption" label-slot bottom-slots
          :error-message="editor.vali(`screenshot.attachments.${index}.caption`)"
          :error="!!editor.vali(`screenshot.attachments.${index}.caption`)">
          <template v-slot:label>
            <label-optional>キャプション</label-optional>
          </template>
        </q-input>
      </div>
      <div class="column" v-if="index !== editor.screenshot.attachments.length - 1">
        <q-btn icon="swap_horiz" flat color="secondary" class="col" @click="swap(index, index + 1)" />
      </div>
    </template>
  </div>
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

export default defineComponent({
  name: 'FormAttachments',
  components: {
    FileManager,
    LabelOptional,
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
        editor.screenshot.attachments = ids.map((id) => mypage.findAttachmentById(id));
      },
    });
    watchEffect(() => {
      // 現在のオーダー順にソート
      editor.screenshot.attachments.sort((a, b) => a.order - b.order);
      // 表示順をオーダーに適用
      editor.screenshot.attachments.map((a, index) => ({ ...a, order: index + 1 }));
    });

    const swap = (a, b) => {
      editor.screenshot.attachments[a].order = b;
      editor.screenshot.attachments[b].order = a;
    };

    return {
      mypage,
      editor,
      attachmentIds,
      swap,
    };
  },
});
</script>
