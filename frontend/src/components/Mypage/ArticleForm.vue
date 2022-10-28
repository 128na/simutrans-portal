<template>
  <q-input label v-model="editor.article.title">
    <template v-slot:label>
      <label-required>タイトル</label-required>
    </template>
  </q-input>
  <div>
    <form-slug v-model="editor.article.slug" :title="editor.article.title" />
  </div>
  <form-thumbnail />
  <component :is="postTypeForm" />
  <form-status />
  <form-reservation />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import FormSlug from 'src/components/Mypage/FormSlug.vue';
import FormStatus from 'src/components/Mypage/FormStatus.vue';
import { defineComponent, computed } from 'vue';
import {
  POST_TYPE_ADDON_INTRODUCTION, POST_TYPE_ADDON_POST, POST_TYPE_PAGE, POST_TYPE_MARKDOWN,
} from 'src/const';
import FormReservation from 'src/components/Mypage/FormReservation.vue';
import FormAddonIntroduction from 'src/components/Mypage/FormAddonIntroduction.vue';
import FormAddonPost from 'src/components/Mypage/FormAddonPost.vue';
import FormMarkdown from 'src/components/Mypage/FormMarkdown.vue';
import FormPage from 'src/components/Mypage/FormPage.vue';
import FormThumbnail from 'src/components/Mypage/FormThumbnail.vue';

export default defineComponent({
  name: 'ArticleForm',
  components: {
    FormSlug,
    FormStatus,
    FormReservation,
    LabelRequired,
    FormThumbnail,
  },
  setup() {
    const editor = useArticleEditStore();

    const postTypeForm = computed(() => {
      switch (editor.article.post_type) {
        case POST_TYPE_ADDON_INTRODUCTION:
          return FormAddonIntroduction;
        case POST_TYPE_ADDON_POST:
          return FormAddonPost;
        case POST_TYPE_PAGE:
          return FormPage;
        case POST_TYPE_MARKDOWN:
          return FormMarkdown;
        default:
          throw new Error(`invalid post type ${editor.article.post_type}`);
      }
    });

    return {
      editor,
      postTypeForm,
    };
  },
});
</script>
