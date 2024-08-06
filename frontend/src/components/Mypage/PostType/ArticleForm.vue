<template>
  <q-input v-model="editor.article.title" label-slot bottom-slots :error-message="editor.vali('article.title')"
    :error="!!editor.vali('article.title')">
    <template v-slot:label>
      <label-required>タイトル</label-required>
    </template>
  </q-input>
  <div>
    <form-slug v-model="editor.article.slug" :title="editor.article.title" />
  </div>
  <form-thumbnail />
  <component :is="postTypeForm" />
  <FormArticleRelations v-model="editor.article.articles">
    <template #validate="slotProps">
      <div v-show="editor.vali(`article.articles.${slotProps.index}.id`)" class="text-negative">
        {{ editor.vali(`article.articles.${slotProps.index}.id`) }}
      </div>
    </template>
  </FormArticleRelations>
  <form-status />
  <form-reservation />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import FormSlug from 'src/components/Mypage/ArticleForm/FormSlug.vue';
import FormStatus from 'src/components/Mypage/ArticleForm/FormStatus.vue';
import { defineComponent, computed } from 'vue';
import {
  POST_TYPE_ADDON_INTRODUCTION, POST_TYPE_ADDON_POST, POST_TYPE_PAGE, POST_TYPE_MARKDOWN,
} from 'src/const';
import FormReservation from 'src/components/Mypage/ArticleForm/FormReservation.vue';
import FormAddonIntroduction from 'src/components/Mypage/PostType/FormAddonIntroduction.vue';
import FormAddonPost from 'src/components/Mypage/PostType/FormAddonPost.vue';
import FormMarkdown from 'src/components/Mypage/PostType/FormMarkdown.vue';
import FormPage from 'src/components/Mypage/PostType/FormPage.vue';
import FormThumbnail from 'src/components/Mypage/ArticleForm/FormThumbnail.vue';
import FormArticleRelations from 'src/components/Mypage/ArticleForm/FormArticleRelations.vue';

export default defineComponent({
  name: 'ArticleForm',
  components: {
    FormSlug,
    FormStatus,
    FormReservation,
    LabelRequired,
    FormThumbnail,
    FormArticleRelations,
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
