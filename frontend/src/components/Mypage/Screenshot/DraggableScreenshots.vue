<template>
  <div class="row q-gutter-sm">
    <template v-for="(attachmentId, index) in sortedAttachments" :key="attachmentId">
      <div>
        <q-img :src="mypage.findAttachmentById(attachmentId).thumbnail" width="256px" />
        <q-input :modelValue="getCaption(attachmentId)" @update:modelValue="updateCaption(attachmentId, $event)"
          label-slot bottom-slots
          :error-message="editor.vali(`screenshot.extra.attachments.${getIndex(attachmentId)}.caption`)"
          :error="!!editor.vali(`screenshot.extra.attachments.${getIndex(attachmentId)}.caption`)">
          <template v-slot:label>
            <label-optional>キャプション</label-optional>
          </template>
        </q-input>
      </div>
      <div class="column" v-if="index !== editor.screenshot.attachments.length - 1">
        <q-btn icon="swap_horiz" flat color="secondary" class="col"
          @click="swap(attachmentId, sortedAttachments[index + 1])" />
      </div>
    </template>
  </div>
</template>
<script>
import {
  defineComponent, watchEffect, computed,
} from 'vue';
import { useScreenshotEditStore } from 'src/store/screenshotEdit';
import { useMypageStore } from 'src/store/mypage';
import LabelOptional from 'src/components/Common/LabelOptional.vue';

export default defineComponent({
  name: 'DraggableScreenshots',
  components: {
    LabelOptional,
  },
  props: {
  },
  setup() {
    const mypage = useMypageStore();
    const editor = useScreenshotEditStore();
    const sortedAttachments = computed(() => [...editor.screenshot.attachments]
      .sort((a, b) => (
        (editor.screenshot.extra.attachments.find((ex) => ex.id === a)?.order ?? 65535)
        - (editor.screenshot.extra.attachments.find((ex) => ex.id === b)?.order ?? 65535)
      )));

    const getCaption = (id) => editor.screenshot.extra.attachments.find((a) => a.id === id)?.caption ?? '';
    const getIndex = (id) => editor.screenshot.extra.attachments.findIndex((a) => a.id === id);
    const updateCaption = (id, val) => {
      const index = getIndex(id);
      if (index !== -1) {
        editor.screenshot.extra.attachments[index].caption = val;
      }
    };

    // todo pinia subscribe
    // watchEffect(() => {
    //   console.log('watchEffect');
    //   const tmp = JSON.parse(JSON.stringify(editor.screenshot.extra.attachments));
    //   tmp.forEach((ex, index) => {
    //     const exists = sortedAttachments.value.findIndex((a) => a === ex.id);
    //     if (exists !== -1) {
    //       if (editor.screenshot.extra.attachments[index]) {
    //         console.log('update');
    //         editor.screenshot.extra.attachments[index].order = exists;
    //       }
    //     } else {
    //       console.log('remove');
    //       editor.screenshot.extra.attachments.splice(exists, 1);
    //     }
    //   });
    //   sortedAttachments.value.forEach((a, index) => {
    //     const exists = editor.screenshot.extra.attachments.find((ex) => a === ex.id);
    //     if (!exists) {
    //       console.log('add');
    //       editor.screenshot.extra.attachments.push({
    //         id: a,
    //         order: index,
    //         caption: '',
    //       });
    //     }
    //   });
    // }, {});

    const swap = (a, b) => {
      const aIdx = editor.screenshot.extra.attachments.findIndex((ex) => ex.id === a);
      const bIdx = editor.screenshot.extra.attachments.findIndex((ex) => ex.id === b);
      [editor.screenshot.extra.attachments[aIdx].order, editor.screenshot.extra.attachments[bIdx].order] = [
        editor.screenshot.extra.attachments[bIdx].order, editor.screenshot.extra.attachments[aIdx].order];
    };

    return {
      mypage,
      editor,
      swap,
      sortedAttachments,
      getCaption,
      updateCaption,
      getIndex,
    };
  },
});
</script>
