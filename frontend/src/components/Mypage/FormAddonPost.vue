<template>
  <q-input label v-model="editor.article.contents.author">
    <template v-slot:label>
      <label-optional>作者</label-optional>
    </template>
  </q-input>
  <form-addon-file />
  <q-input label type="textarea" v-model="editor.article.contents.description">
    <template v-slot:label>
      <label-required>説明</label-required>
    </template>
  </q-input>
  <q-btn flat :disabled="!file" @click="handleCopyFromZip">Zipファイル内のreadmeからコピー</q-btn>
  <q-input label type="textarea" v-model="editor.article.contents.thanks">
    <template v-slot:label>
      <label-optional>謝辞・参考にしたアドオン</label-optional>
    </template>
  </q-input>
  <q-input label type="textarea" v-model="editor.article.contents.license">
    <template v-slot:label>
      <label-optional>ライセンスその他</label-optional>
    </template>
  </q-input>
  <form-addon-categories />
  <label-optional>タグ</label-optional>
  <form-tag v-model="editor.article.tags" />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent, computed } from 'vue';
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import FormAddonCategories from 'src/components/Mypage/FormAddonCategories.vue';
import LabelOptional from 'src/components/Common/LabelOptional.vue';
import FormTag from 'src/components/Mypage/FormTag.vue';
import FormAddonFile from 'src/components/Mypage/FormAddonFile.vue';
import { useMypageStore } from 'src/store/mypage';

export default defineComponent({
  name: 'FormAddonPost',
  components: {
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
    const handleCopyFromZip = () => {
      /**
       * @type {Object}
       */
      const readmes = file.value?.fileInfo?.readmes;

      const text = Object.entries(readmes)
        .reduce((t, [filename, readme]) => `${t}\n------\n#${filename}\n${readme.join('')}`, '');

      editor.article.contents.description += text;
    };

    return {
      editor,
      file,
      handleCopyFromZip,
    };
  },
});
</script>
