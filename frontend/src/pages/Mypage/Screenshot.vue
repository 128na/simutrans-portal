<template>
  <q-page v-if="mypage.screenshotsReady && mypage.attachmentsReady" class="q-ma-md">
    <text-title>スクリーンショット一覧
      <q-btn color="primary" @click="editor.createScreenshot">新規投稿</q-btn>
    </text-title>
    <div class="row q-gutter-md">
      <template v-for="s in mypage.screenshots" :key="s.id">
        <div>
          <div>
            <q-badge :color="isPublish(s) ? 'positive' : 'negative'">
              {{ isPublish(s) ? "公開" : '非公開' }}
            </q-badge>
            {{ s.title }}
          </div>
          <ScreenshotThumbnail :screenshot="s" :attachments="mypage.attachments" />
          <div class="q-mt-sm">
            <q-btn-group>
              <q-btn outline color="primary" :href="`./screenshots/${s.id}`" target="_blank">
                <q-icon name="launch" size="small" />表示</q-btn>
              <q-btn outline color="secondary" @click="editor.selectScreenshot(s)">編集</q-btn>
              <q-btn outline color="negative" @click="destroy(s.id)">削除</q-btn>
            </q-btn-group>
          </div>
        </div>
      </template>
    </div>
    <p v-show="mypage.screenshots.length < 1">スクリーンショットがありません</p>
    <ScreenshotEditor />
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
import ScreenshotThumbnail from 'src/components/Common/Screenshot/ScreenshotThumbnail.vue';

export default defineComponent({
  name: 'PageScreenshot',
  components: {
    TextTitle,
    ScreenshotEditor,
    ScreenshotThumbnail,
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

    const destroy = async (id) => {
      try {
        if (window.confirm('削除しますか？')) {
          const data = await editor.destroy(id);
          mypage.screenshots = data;
        }
      } catch {
        // do nothing
      }
    };
    const isPublish = (screenshot) => screenshot.status === 'Publish';
    return {
      mypage,
      editor,
      destroy,
      isPublish,
    };
  },
});
</script>
