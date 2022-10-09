<template>
  <div>
    <text-pre class="text-negative">{{message}}</text-pre>
    <q-btn v-show="canRetry" flat round size="sm" icon="replay" @click="$emit('retry')" />
  </div>
</template>
<script>

import { defineComponent, computed } from 'vue';
import TextPre from './TextPre.vue';

export default defineComponent({
  name: 'ApiErrorMessage',
  props: {
    message: {
      type: [String, Object],
      default: '',
    },
  },
  setup(props, context) {
    const renderMessage = computed(() => {
      if (typeof props.message === 'string') {
        return props.message;
      }
      return Object.entries(props.message)
        .map((k, v) => `${k} ${v.join(', ')}`)
        .join('\n');
    });
    const canRetry = computed(() => context.attrs.onRetry);
    return { renderMessage, canRetry };
  },
  components: { TextPre },
});
</script>
