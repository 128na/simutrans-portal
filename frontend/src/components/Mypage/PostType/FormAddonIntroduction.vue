<template>
  <q-input label-slot v-model="editor.article.contents.author" bottom-slots
    :error-message="editor.vali('article.contents.author')" :error="!!editor.vali('article.contents.author')">
    <template v-slot:label>
      <label-required>作者</label-required>
    </template>
  </q-input>
  <q-input label-slot type="url" v-model="editor.article.contents.link" bottom-slots
    :error-message="editor.vali('article.contents.author')" :error="!!editor.vali('article.contents.link')">
    <template v-slot:label>
      <label-required>リンク先URL</label-required>
    </template>
  </q-input>
  <input-countable label-slot v-model="editor.article.contents.description" :maxLength="2048" bottom-slots
    :error-message="editor.vali('article.contents.author')" :error="!!editor.vali('article.contents.description')">
    <label-required>説明</label-required>
  </input-countable>
  <input-countable label-slot v-model="editor.article.contents.thanks" :maxLength="2048" bottom-slots
    :error-message="editor.vali('article.contents.author')" :error="!!editor.vali('article.contents.thanks')">
    <label-optional>謝辞・参考にしたアドオン</label-optional>
  </input-countable>
  <input-countable label-slot v-model="editor.article.contents.license" :maxLength="2048" bottom-slots
    :error-message="editor.vali('article.contents.author')" :error="!!editor.vali('article.contents.license')">
    <label-optional>ライセンスその他</label-optional>
  </input-countable>
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
import FormAddonCategories from 'src/components/Mypage/ArticleForm/FormAddonCategories.vue';
import FormTag from 'src/components/Mypage/ArticleForm/FormTag.vue';
import InputCountable from 'src/components/Common/Input/InputCountable.vue';
import LabelOptional from 'src/components/Common/LabelOptional.vue';

export default defineComponent({
  name: 'FormAddonIntroduction',
  components: {
    InputCountable,
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
