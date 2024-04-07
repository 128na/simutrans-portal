<template>
  <q-dialog :modelValue="!!editor.screenshot" persistent maximized @hide="editor.reset">
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
          <span v-show="editor.vali('screenshot.attachments')" class="text-negative">
            {{ editor.vali('screenshot.attachments') }}
          </span>
          <FormAttachments />

          <FormArticleRelations v-model="editor.screenshot.articles">
            <template #validate="slotProps">
              <div v-show="editor.vali(`screenshot.articles.${slotProps.index}.id`)" class="text-negative">
                {{ editor.vali(`screenshot.articles.${slotProps.index}.id`) }}
              </div>
            </template>
          </FormArticleRelations>
          <FormLinks />
          <FormStatus />
          <FormNotify />
          <div class="q-py-lg">
            <q-btn color="primary" @click="save">{{ editor.screenshot.id ? '更新' : '投稿' }}</q-btn>
          </div>
        </q-page>
      </q-page-container>
    </q-layout>
  </q-dialog>
</template>
<script>
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import { defineComponent } from 'vue';
import FormArticleRelations from 'src/components/Mypage/Screenshot/FormArticleRelations.vue';
import FormLinks from 'src/components/Mypage/Screenshot/FormLinks.vue';
import { useScreenshotEditStore } from 'src/store/screenshotEdit';
import InputCountable from 'src/components/Common/Input/InputCountable.vue';
import { useMypageStore } from 'src/store/mypage';
import FormStatus from './FormStatus.vue';
import FormAttachments from './FormAttachments.vue';
import FormNotify from './FormNotify.vue';

export default defineComponent({
  name: 'ScreenshotEditor',
  components: {
    InputCountable,
    LabelRequired,
    FormArticleRelations,
    FormLinks,
    FormStatus,
    FormAttachments,
    FormNotify,
  },
  props: {
  },
  setup() {
    const mypage = useMypageStore();
    const editor = useScreenshotEditStore();
    const save = async () => {
      try {
        const data = await editor.save();
        mypage.screenshots = data;
        editor.screenshot = null;
        mypage.fetchAttachments();
      } catch {
        // do nothing
      }
    };

    return {
      mypage,
      editor,
      save,
    };
  },
});
</script>
