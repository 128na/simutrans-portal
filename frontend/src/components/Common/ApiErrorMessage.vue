<template>
  <div>
    <q-btn v-show="canRetry" flat round size="sm" icon="replay" @click="retry" />
    {{message}}
  </div>
</template>
<script>

import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'ApiErrorMessage',
  props: {
    message: {
      type: [String, Object],
      default: '',
    },
    retry: {
      type: Function,
      required: false,
    },
  },
  setup(props) {
    const renderMessage = computed(() => {
      if (typeof props.message === 'string') {
        return props.message;
      }
      return Object.entries(props.message)
        .map((k, v) => `${k} ${v.join(', ')}`)
        .join('\n');
    });
    const canRetry = computed(() => typeof props.retry === 'function');
    return { renderMessage, canRetry };
  },
});
</script>
