<template>
  <q-img :src="attachmentUrl" width="256px" />
</template>
<script>
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'ScreenshotThumbnail',
  components: {
  },
  props: {
    screenshot: {
      type: Object,
      required: true,
    },
    attachments: {
      type: Array,
      default: () => [],
    },
  },
  setup(props) {
    const attachmentUrl = computed(() => {
      const attachmentOrData = props.screenshot.attachments[0];
      if (attachmentOrData?.url) {
        return attachmentOrData.url;
      }
      return props.attachments.find((a) => a.id === attachmentOrData.id)?.url;
    });

    return {
      attachmentUrl,
    };
  },
});
</script>
