<template>
  <q-list>
    <template v-for="(section, index) in editor.article.contents.sections" :key="index">
      <q-item class="q-pa-none">
        <q-item-section>
          <q-item-label>
            <template v-if="section.type === 'text'">
              <input-countable label-slot v-model="section.text" :maxLength="2048">
                <label-required>内容</label-required>
              </input-countable>
            </template>
            <template v-else-if="section.type === 'caption'">
              <q-input label-slot v-model="section.caption">
                <template v-slot:label>
                  <label-required>見出し</label-required>
                </template>
              </q-input>
            </template>
            <template v-else-if="section.type === 'url'">
              <q-input label-slot v-model="section.url" type="url">
                <template v-slot:label>
                  <label-required>URL</label-required>
                </template>
              </q-input>
            </template>
            <template v-else-if="section.type === 'image'">
              <label-required>画像</label-required>
              <q-input :model-value="getFilename(section.id)" readonly>
                <template v-slot:append>
                  <q-icon name="close" class="cursor-pointer q-mr-sm" @click="section.id = null" />
                  <file-manager v-model="section.id" onlyImage />
                </template>
              </q-input>
            </template>
          </q-item-label>
          <q-item-label caption class="text-right">
            <q-btn-group flat>
              <q-btn :disable="index === 0" @click="editor.changeSectionOrder(index, index - 1)" icon="arrow_upward" />
              <q-btn :disable="index === editor.article.contents.sections.length - 1"
                @click="editor.changeSectionOrder(index, index + 1)" icon="arrow_downward" />
              <q-btn @click="editor.deleteSection(index)" icon="close" text-color="negative" />
            </q-btn-group>
          </q-item-label>
        </q-item-section>
      </q-item>
    </template>
  </q-list>
  <div>項目追加</div>
  <q-btn-group>
    <q-btn @click="editor.addSection('caption')">見出し</q-btn>
    <q-btn @click="editor.addSection('text')">本文</q-btn>
    <q-btn @click="editor.addSection('image')">画像</q-btn>
    <q-btn @click="editor.addSection('url')">URL</q-btn>
  </q-btn-group>
  <form-page-categories />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { defineComponent } from 'vue';
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import { useMypageStore } from 'src/store/mypage';
import FileManager from 'src/components/Mypage/FileManager.vue';
import FormPageCategories from 'src/components/Mypage/ArticleForm/FormPageCategories.vue';
import InputCountable from 'src/components/Common/Input/InputCountable.vue';

export default defineComponent({
  name: 'FormPage',
  components: {
    InputCountable,
    LabelRequired,
    FileManager,
    FormPageCategories,
  },
  setup() {
    const editor = useArticleEditStore();
    const mypage = useMypageStore();

    const getFilename = (id) => {
      if (!id) {
        return '未選択';
      }
      return mypage.findAttachmentById(id)?.original_name || 'ファイルが見つかりません';
    };

    return {
      editor,
      getFilename,
    };
  },
});
</script>
