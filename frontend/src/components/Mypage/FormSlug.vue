<template>
  <q-input label="URL" v-model="rawSlug" >
    <template v-slot:append>
      <q-btn flat color="secondary" @click="rawSlug=title">タイトルからコピー</q-btn>
    </template>
    </q-input>
  <div>url: {{url}}</div>
</template>
<script>
import { useAppInfo } from 'src/composables/appInfo';
import { defineComponent, computed } from 'vue';

const regReplace = /(!|"|#|\$|%|&|'|\(|\)|\*|\+|,|\/|:|;|<|=|>|\?|@|\[|\\|\]|\^|`|\{|\||\}|\s|\.)+/gi;
export default defineComponent({
  name: 'FormSlug',
  components: {},
  props: {
    modelValue: {
      type: String,
      default: '',
    },
    title: {
      type: String,
      default: '',
    },
  },
  setup(props, { emit }) {
    const { backendUrl } = useAppInfo();
    const rawSlug = computed({
      get() { return decodeURI(props.modelValue); },
      set(val) {
        const replaced = val.toLowerCase().replace(regReplace, '-');
        emit('update:model-value', encodeURI(replaced));
      },
    });
    const url = computed(() => `${backendUrl}/articles/${props.modelValue}`);
    return {
      rawSlug,
      url,
    };
  },
});
</script>
