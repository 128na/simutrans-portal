<template>
  <div>
    <page-title>ブックマーク一覧</page-title>
    <page-description>
      作成したブックマークの管理ができます<br>
      <b-button
        variant="primary"
        :to="route_edit_bookmark()"
      >
        新規作成
      </b-button>
    </page-description>
    <div v-if="ready">
      <div v-if="!isVerified">
        <need-verify />
      </div>
      <div v-else>
        <page-sub-title>一括ダウンロード</page-sub-title>
        <page-description>
          ブックマーク内のアドオンを一括でダウンロードできます。<br>
          記事数が多いとファイルの生成には数分かかることがあります。
          <bulk-zip-downloader
            :target_type="targetType"
            :target_id="targetId"
            class="mb-3"
          >
            <template #default="slotProps">
              <b-select
                v-model="targetId"
                :options="options"
                :disabled="slotProps.processing"
              />
            </template>
          </bulk-zip-downloader>
        </page-description>
        <page-sub-title>ブックマーク一覧</page-sub-title>
        <div>
          <bookmark-table :bookmarks="bookmarks" />
        </div>
      </div>
    </div>
    <loading-message v-else />
  </div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
import { validateLogin } from '../../mixins/auth';
import { TARGET_TYPE_BOOKMARK } from '../../../const';
export default {
  mixins: [validateLogin],
  data() {
    return {
      targetId: null
    };
  },
  created() {
    if (this.isLoggedIn && !this.bookmarksLoaded) {
      this.fetchBookmarks();
    }
  },
  computed: {
    ...mapGetters([
      'isLoggedIn',
      'initialized',
      'isVerified',
      'bookmarksLoaded',
      'bookmarks'
    ]),
    ready() {
      return this.bookmarksLoaded;
    },
    targetType() {
      return TARGET_TYPE_BOOKMARK;
    },
    options() {
      return this.bookmarks.map((b) => {
        return { value: b.id, text: b.title };
      });
    }
  },
  methods: {
    ...mapActions(['fetchBookmarks'])
  }
};
</script>
