<template>
  <label-optional class="q-mt-sm">自動通知</label-optional>
  <div v-if="published">初めて公開するときのみ選べます。</div>
  <q-checkbox v-else-if="canNotify" v-model="editor.shouldNotify" label="投稿時にSNS通知する" />
  <div v-else>公開状態が「公開」のときのみ選べます。</div>
</template>
<script>
import { defineComponent, computed } from 'vue';
import { useScreenshotEditStore } from 'src/store/screenshotEdit';
import LabelOptional from '../../Common/LabelOptional.vue';

export default defineComponent({
  name: 'FormNotify',
  components: { LabelOptional },
  setup() {
    const editor = useScreenshotEditStore();
    const canNotify = computed(() => editor.screenshot.status === 'Publish');
    const published = !!editor.screenshot.published_at;
    return {
      editor,
      canNotify,
      published,
    };
  },
});
</script>
