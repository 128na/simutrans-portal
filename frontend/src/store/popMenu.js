import { defineStore } from 'pinia';
import { computed, reactive } from 'vue';

/**
 * ポップアップメニュー
 */
export const usePopMenuStore = defineStore('popMenu', () => {
  const state = reactive({
    row: null,
    x: null,
    y: null,
  });

  const style = computed(() => {
    const w = window.innerWidth;
    const l = w / 2 > state.x;
    return {
      top: `${state.y}px`,
      [l ? 'left' : 'right']: `${l ? state.x + 20 : w - state.x}px`,
    };
  });
  const show = computed(() => !!state.row);

  const open = (event, r) => {
    state.row = state.row && state.row.id === r.id ? null : r;
    state.x = event.clientX;
    state.y = event.clientY;
  };

  const close = () => {
    state.row = null;
  };

  return {
    state,
    show,
    style,
    open,
    close,
  };
});
