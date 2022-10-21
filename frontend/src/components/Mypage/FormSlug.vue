<template>
  <q-input label v-model="rawSlug">
    <template v-slot:label>
      <label-required>スラッグ</label-required>
    </template>
    <template v-slot:append>
      <q-btn flat color="secondary" @click="rawSlug=title">タイトルからコピー</q-btn>
    </template>
  </q-input>
  <div class="word-break">{{url}}</div>
</template>
<script>
import { useAppInfo } from 'src/composables/appInfo';
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import { computed, defineComponent } from 'vue';

const regReplace = /(!|"|#|\$|%|&|'|\(|\)|\*|\+|,|\/|:|;|<|=|>|\?|@|\[|\\|\]|\^|`|\{|\||\}|\s|\.)+/gi;
export default defineComponent({
  name: 'FormSlug',
  components: { LabelRequired },
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
