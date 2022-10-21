<template>
  <q-dialog v-model="show">
    <q-card>
    </q-card>
  </q-dialog>
  <q-btn :label="label" color="secondary" @click="show=true" />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'FileManager',
  props: {
    modelValue: {
      type: Array,
      default: () => [],
    },
    label: {
      type: String,
      default: '選択',
    },
  },
  setup() {
    const editor = useArticleEditStore();
    const show = ref(false);
    return {
      editor,
      show,
    };
  },
});
</script>
