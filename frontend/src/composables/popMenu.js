import { computed, ref } from 'vue';

/**
 * マイページ
 */
export const usePopMenu = () => {
  const row = ref(null);
  const top = ref(null);
  const left = ref(null);

  const style = computed(() => ({ top: top.value, left: left.value }));
  const show = computed(() => !!row.value);

  const open = (event, r) => {
    row.value = row.value && row.value.id === r.id ? null : r;
    top.value = `${event.clientY}px`;
    left.value = `${event.clientX}px`;
  };

  const close = () => {
    row.value = null;
  };

  return {
    row,
    show,
    style,
    open,
    close,
  };
};
