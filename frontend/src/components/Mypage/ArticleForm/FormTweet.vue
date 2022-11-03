<template>
  <label-optional>自動ツイート</label-optional>
  <q-checkbox v-if="canTweet" v-model="editor.tweet" label="投稿時にツイートする" />
  <div v-else>公開状態が「公開」のときのみ選べます。</div>
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, computed } from 'vue';
import LabelOptional from '../../Common/LabelOptional.vue';

export default defineComponent({
  name: 'FormTweet',
  components: { LabelOptional },
  setup() {
    const editor = useArticleEditStore();
    const canTweet = computed(() => editor.article.status === 'publish');
    return {
      editor,
      canTweet,
    };
  },
});
</script>
