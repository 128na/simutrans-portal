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
    <q-btn @click="editor.togglePreview()">{{
    editor.preview ? "プレビュー非表示" : "プレビュー表示"
    }}</q-btn>
  </div>
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import FormSlug from 'src/components/Mypage/FormSlug.vue';
import FormStatus from 'src/components/Mypage/FormStatus.vue';
import { defineComponent } from 'vue';
import FormReservation from './FormReservation.vue';

export default defineComponent({
  name: 'ArticleForm',
  components: { FormSlug, FormStatus, FormReservation },
  setup() {
    const editor = useArticleEditStore();

    return {
      editor,
    };
  },
});
</script>
