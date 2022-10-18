<template>
  <q-input label="タイトル" v-model="editor.article.title" />
  <div>
    <form-slug v-model="editor.article.slug" :title="editor.article.title" />
  </div>
  <div>todo: サムネ選択機能実装</div>
  <div>
    <form-status />
  </div>
  <div>
    <form-reservation />
  </div>
  <div>
    <form-tweet />
  </div>
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import FormSlug from 'src/components/Mypage/FormSlug.vue';
import FormStatus from 'src/components/Mypage/FormStatus.vue';
import { defineComponent } from 'vue';
import FormReservation from './FormReservation.vue';
import FormTweet from './FormTweet.vue';

export default defineComponent({
  name: 'ArticleForm',
  components: {
    FormSlug, FormStatus, FormReservation, FormTweet,
  },
  setup() {
    const editor = useArticleEditStore();

    return {
      editor,
    };
  },
});
</script>
