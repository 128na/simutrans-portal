<template>
  <q-dialog :modelValue="!!editor.screenshot" persistent maximized>
    <q-layout view="hHh lpR fFf">
      <q-header elevated class="bg-dark text-white">
        <q-toolbar>
          <q-toolbar-title>{{ editor.screenshot.id ? '編集' : '新規投稿' }}</q-toolbar-title>
          <q-space />
          <q-btn dense flat icon="close" v-close-popup />
        </q-toolbar>
      </q-header>
      <q-page-container class="bg-white">
        <q-page class="q-ma-md">
          <q-input label-slot type="url" v-model="editor.screenshot.title" bottom-slots
            :error-message="editor.vali('screenshot.title')" :error="!!editor.vali('screenshot.title')">
            <template v-slot:label>
              <label-required>タイトル</label-required>
            </template>
          </q-input>
          <input-countable label-slot v-model="editor.screenshot.description" :maxLength="2048" bottom-slots
            :error-message="editor.vali('screenshot.description')" :error="!!editor.vali('screenshot.description')">
            <label-required>説明</label-required>
          </input-countable>
          <label-required>画像（10枚まで）</label-required>

          <div class="row q-gutter-sm">
            <template v-for="attachmentId in editor.screenshot.attachments" :key="attachmentId">
              <div class="">
                <q-img :src="mypage.findAttachmentById(attachmentId).thumbnail" width="256px" />
                <div>{{ mypage.findAttachmentById(attachmentId).original_name }}</div>
              </div>
            </template>
          </div>
          <div class="q-my-md">
            <FileManager v-model="editor.screenshot.attachments" :onlyImage="true"></FileManager>
          </div>

          <FormArticleRelations />
          <FormLinks />
          <hr />
          <q-btn color="primary">投稿</q-btn>
        </q-page>
      </q-page-container>
    </q-layout>
  </q-dialog>
</template>
<script>
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import { defineComponent } from 'vue';
import FileManager from 'src/components/Mypage/FileManager.vue';
import FormArticleRelations from 'src/components/Mypage/Screenshot/FormArticleRelations.vue';
import FormLinks from 'src/components/Mypage/Screenshot/FormLinks.vue';
import { useScreenshotEditStore } from 'src/store/screenshotEdit';
import InputCountable from 'src/components/Common/Input/InputCountable.vue';
import { useMypageStore } from 'src/store/mypage';

export default defineComponent({
  name: 'ScreenshotEditor',
  components: {
    InputCountable,
    LabelRequired,
    FileManager,
    FormArticleRelations,
    FormLinks,
  },
  props: {
  },
  setup() {
    const mypage = useMypageStore();
    const editor = useScreenshotEditStore();
    return {
      mypage,
      editor,
    };
  },
});
</script>
