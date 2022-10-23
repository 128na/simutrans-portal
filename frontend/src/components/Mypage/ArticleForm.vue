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
  <form-tweet />
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
import FormReservation from './FormReservation.vue';
import FormTweet from './FormTweet.vue';
import FormAddonIntroduction from './FormAddonIntroduction.vue';
import FormAddonPost from './FormAddonPost.vue';
import FormMarkdown from './FormMarkdown.vue';
import FormThumbnail from './FormThumbnail.vue';

export default defineComponent({
  name: 'ArticleForm',
  components: {
    FormSlug,
    FormStatus,
    FormReservation,
    FormTweet,
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
          throw new Error('未実装');
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
