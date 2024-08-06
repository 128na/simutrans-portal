<template>
  <q-input label-slot v-model="rawSlug" bottom-slots :error-message="editor.vali('article.slug')"
    :error="!!editor.vali('article.slug')">
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
  <div v-if="editor.slugChanged">
    <q-checkbox v-model="editor.followRedirect" class="text-dark q-mr-sm" label="リダイレクト追加">
      <q-tooltip>
        古いパーマリンクからのアクセスを新しいパーマリンクへ転送します。<br />SNS通知など古いリンクを修正できない場合にリンク切れしなくなります。
      </q-tooltip>
    </q-checkbox>
  </div>
</template>
<script>
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import { useAuthStore } from 'src/store/auth';
import { useArticleEditStore } from 'src/store/articleEdit';
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
    const editor = useArticleEditStore();
    const rawSlug = computed({
      get() { return decodeURI(props.modelValue); },
      set(val) {
        const replaced = val.toLowerCase().replace(regReplace, '-');
        emit('update:model-value', encodeURI(replaced));
      },
    });
    const url = computed(() => `/users/${auth.user.nickname || auth.user.id}/${props.modelValue}`);

    return {
      editor,
      rawSlug,
      url,
    };
  },
});
</script>
