<template>
  <q-input label-slot v-model="editor.article.contents.author" bottom-slots
    :error-message="editor.vali('article.contents.author')" :error="!!editor.vali('article.contents.author')">
    <template v-slot:label>
      <label-optional>作者</label-optional>
    </template>
  </q-input>
  <form-addon-file />
  <input-countable label-slot v-model="editor.article.contents.description" :maxLength="2048" bottom-slots
    :error-message="editor.vali('article.contents.description')" :error="!!editor.vali('article.contents.description')">
    <label-required>説明</label-required>
  </input-countable>
  <q-btn flat color="secondary" :disabled="!file" @click="handleCopyFromZip">Zipファイル内のreadmeから追加する</q-btn>
  <q-btn flat color="secondary" :disabled="!file" @click="handleAiFromZip">AIでZipファイル内のreadmeから文章生成する</q-btn>
  <input-countable label-slot v-model="editor.article.contents.thanks" :maxLength="2048" bottom-slots
    :error-message="editor.vali('article.contents.thanks')" :error="!!editor.vali('article.contents.thanks')">
    <label-optional>謝辞・参考にしたアドオン</label-optional>
  </input-countable>
  <input-countable label-slot v-model="editor.article.contents.license" :maxLength="2048" bottom-slots
    :error-message="editor.vali('article.contents.license')" :error="!!editor.vali('article.contents.license')">
    <label-optional>ライセンスその他</label-optional>
  </input-countable>
  <form-addon-categories />
  <label-optional>タグ</label-optional>
  <form-tag v-model="editor.article.tags" />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, computed } from 'vue';
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import InputCountable from 'src/components/Common/Input/InputCountable.vue';
import FormAddonCategories from 'src/components/Mypage/ArticleForm/FormAddonCategories.vue';
import LabelOptional from 'src/components/Common/LabelOptional.vue';
import FormTag from 'src/components/Mypage/ArticleForm/FormTag.vue';
import FormAddonFile from 'src/components/Mypage/ArticleForm/FormAddonFile.vue';
import { useMypageStore } from 'src/store/mypage';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';

export default defineComponent({
  name: 'FormAddonPost',
  components: {
    InputCountable,
    FormAddonCategories,
    FormTag,
    FormAddonFile,
    LabelRequired,
    LabelOptional,
  },
  setup() {
    const editor = useArticleEditStore();
    const mypage = useMypageStore();
    const file = computed(() => (editor.article.contents.file ? mypage.findAttachmentById(editor.article.contents.file) : null));
    const text = computed(() => {
      const readmes = file.value?.fileInfo?.readmes;
      return Object.entries(readmes)
        .reduce((t, [filename, readme]) => `${t}\n------\n#${filename}\n${readme.join('')}`, '');
    });

    const handleCopyFromZip = () => {
      editor.article.contents.description += text.value;
    };

    const api = useMypageApi();
    const handler = useApiHandler();
    const handleAiFromZip = async () => {
      handler.handleWithLoading({
        doRequest: () => api.aiDescription(text.value),
        done: (res) => {
          editor.article.contents.description += res.data.description;
        },
        failedMessage: '生成に失敗しました',
      });
    };

    return {
      editor,
      file,
      handleCopyFromZip,
      handleAiFromZip,
    };
  },
});
</script>
