<template>
  <q-input label-slot v-model="rawSlug" bottom-slots>
    <template v-slot:label>
      <label-required>パーマリンク</label-required>
    </template>
    <template v-slot:append>
      <q-btn flat color="secondary" @click="rawSlug = title">タイトルからコピー</q-btn>
    </template>
    <template v-slot:hint>
      <div class="word-break">{{ url }}</div>
    </template>
  </q-input>
</template>
<script>
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import { useAuthStore } from 'src/store/auth';
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
    const auth = useAuthStore();
    const rawSlug = computed({
      get() { return decodeURI(props.modelValue); },
      set(val) {
        const replaced = val.toLowerCase().replace(regReplace, '-');
        emit('update:model-value', encodeURI(replaced));
      },
    });
    const url = computed(() => `/users/${auth.user.nickname || auth.user.id}/${props.modelValue}`);
    return {
      rawSlug,
      url,
    };
  },
});
</script>
