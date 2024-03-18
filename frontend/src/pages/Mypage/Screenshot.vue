<template>
  <q-page v-if="mypage.screenshotsReady && mypage.attachmentsReady" class="q-ma-md">
    <text-title>投稿スクリーンショット一覧</text-title>
    <template v-for="s in mypage.screenshots" :key="s.id">
      <div>
        {{ s.title }}
        <q-btn color="secondary" @click="editor.selectScreenshot(s)">編集</q-btn>
      </div>
    </template>
    <p v-show="mypage.screenshots.length < 1">スクリーンショットがありません</p>
    <ScreenshotEditor />
    <q-btn color="primary" @click="editor.createScreenshot">新規投稿</q-btn>
  </q-page>
</template>

<script>
import { useMypageStore } from 'src/store/mypage';
import { defineComponent } from 'vue';
import { useAuthStore } from 'src/store/auth';
import { useMeta } from 'src/composables/meta';
import TextTitle from 'src/components/Common/Text/TextTitle.vue';
import ScreenshotEditor from 'src/components/Mypage/Screenshot/ScreenshotEditor.vue';
import { useScreenshotEditStore } from 'src/store/screenshotEdit';

export default defineComponent({
  name: 'PageScreenshot',
  components: {
    TextTitle,
    ScreenshotEditor,
  },
  setup() {
    const auth = useAuthStore();
    const mypage = useMypageStore();
    if (auth.validateAuth()) {
      mypage.fetchArticles();
      mypage.fetchScreenshots();
      mypage.fetchAttachments();
    }
    const editor = useScreenshotEditStore();

    const meta = useMeta();
    meta.setTitle('投稿スクリーンショット一覧');

    return {
      mypage,
      editor,
    };
  },
});
</script>
