<template>
  <q-input label v-model="editor.article.contents.author">
    <template v-slot:label>
      <label-required>作者</label-required>
    </template>
  </q-input>
  <q-input label type="url" v-model="editor.article.contents.link">
    <template v-slot:label>
      <label-required>リンク先URL</label-required>
    </template>
  </q-input>
  <q-input label type="textarea" v-model="editor.article.contents.description">
    <template v-slot:label>
      <label-required>説明</label-required>
    </template>
  </q-input>
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
  <label-optional>掲載許可</label-optional>
  <q-checkbox label="この記事は作者の許可を得てまたは作者自身により掲載しています。" type="textarea" v-model="editor.article.contents.agreement" />
  <label-optional>自動リンク切れチェック</label-optional>
  <q-checkbox label="自動リンク切れチェックの対象外にする" type="textarea" v-model="editor.article.contents.exclude_link_check" />
  <form-addon-categories />
  <label-optional>タグ</label-optional>
  <form-tag v-model="editor.article.tags" />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent } from 'vue';
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import FormAddonCategories from 'src/components/Mypage/FormAddonCategories.vue';
import FormTag from 'src/components/Mypage/FormTag.vue';
import LabelOptional from '../Common/LabelOptional.vue';

export default defineComponent({
  name: 'FormAddonIntroduction',
  components: {
    FormAddonCategories,
    FormTag,
    LabelRequired,
    LabelOptional,
  },
  setup() {
    const editor = useArticleEditStore();

    return {
      editor,
    };
  },
});
</script>
