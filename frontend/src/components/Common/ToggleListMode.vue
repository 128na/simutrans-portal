<template>
  <div @click="handle(mode.next)">
    <q-icon :name="mode.icon" size="sm" class="cursor-pointer" />
    <q-tooltip>{{ mode.tooltip}}</q-tooltip>
  </div>
</template>
<script>

import { defineComponent, computed } from 'vue';
import { useQuasar } from 'quasar';

const modes = {
  gallery: { icon: 'image', next: 'list', tooltip: 'ギャラリー' },
  list: { icon: 'list', next: 'show', tooltip: 'リスト' },
  show: { icon: 'subject', next: 'gallery', tooltip: '詳細' },
};

export default defineComponent({
  name: 'ToggleListMode',
  props: {
    modelValue: {
      type: String,
      required: true,
    },
  },
  setup(props, { emit }) {
    const $q = useQuasar();
    const handle = (val) => {
      emit('update:modelValue', val);
      $q.localStorage.set('front.listMode', val);
    };
    const prevMode = $q.localStorage.getItem('front.listMode');
    if (prevMode) {
      emit('update:modelValue', prevMode);
    }

    return {
      mode: computed(() => modes[props.modelValue] || modes.list),
      handle,
    };
  },
});
</script>
