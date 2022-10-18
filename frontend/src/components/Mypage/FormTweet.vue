<template>
  <div>
    <div>自動ツイート</div>
    <q-checkbox v-if="canTweet" v-model="editor.tweet" label="投稿時にツイートする" />
    <div v-else>公開状態が「公開」のときのみ選べます。</div>
  </div>
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'FormTweet',
  components: {},
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
