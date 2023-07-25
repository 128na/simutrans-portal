<template>
  <label-optional>自動通知</label-optional>
  <q-checkbox v-if="canNotify" v-model="editor.shouldNotify" label="投稿時にSNS通知する" />
  <div v-else>公開状態が「公開」のときのみ選べます。</div>
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, computed } from 'vue';
import LabelOptional from '../../Common/LabelOptional.vue';

export default defineComponent({
  name: 'FormNotify',
  components: { LabelOptional },
  setup() {
    const editor = useArticleEditStore();
    const canNotify = computed(() => editor.article.status === 'publish');
    return {
      editor,
      canNotify,
    };
  },
});
</script>
