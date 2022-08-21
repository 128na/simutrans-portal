<template>
  <b-row v-if="ready">
    <b-col class="w-50">
      <page-title>編集</page-title>
      <component :is="componentName" :article="copy">
        <form-status :article="copy" :can-reservation="canReservation" />
        <form-reservation :article="copy" />
        <b-form-group>
          <template slot="label">
            <badge-optional />
            更新日時
          </template>
          <b-form-checkbox v-model="without_update_modified_at">
            記事保存時に更新日時を更新しない
          </b-form-checkbox>
        </b-form-group>
        <b-form-group>
          <template slot="label">
            <badge-optional />
            自動ツイート
          </template>
          <template v-if="canTweet">
            <b-form-checkbox v-model="should_tweet">
              記事公開時にツイートする
            </b-form-checkbox>
          </template>
          <template v-else>
            公開状態が「公開」かつ更新日時を更新するときのみ選べます。
          </template>
        </b-form-group>
        <b-form-group>
          <fetching-overlay>
            <b-button @click.prevent="handlePreview">
              {{ show_preview ? 'プレビュー非表示' : 'プレビュー表示' }}
            </b-button>
          </fetching-overlay>
          <fetching-overlay>
            <b-button variant="primary" @click.prevent="handleUpdate">
              「{{ articleStatusText }}」で保存
            </b-button>
          </fetching-overlay>
        </b-form-group>
      </component>
    </b-col>
    <b-col class="w-50" v-show="show_preview">
      <article-preview :article="copy" />
    </b-col>
  </b-row>
  <loading-message v-else />
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
import { validateVerified } from '../../mixins/auth';
import { editor } from '../../mixins/editor';
export default {
  mixins: [validateVerified, editor],
  data() {
    return {
      should_tweet: false,
      without_update_modified_at: false,
      show_preview: true
    };
  },
  watch: {
    articlesLoaded(val) {
      if (val) {
        this.setCopy(this.selected_article);
      }
    }
  },
  created() {
    if (this.isVerified) {
      if (!this.articlesLoaded) {
        this.fetchArticles();
      } else {
        this.setCopy(this.selected_article);
      }
      if (!this.optionsLoaded) {
        this.fetchOptions();
      }
      if (!this.attachmentsLoaded) {
        this.fetchAttachments();
      }
      if (!this.tagsLoaded) {
        this.fetchTags();
      }
    }
  },
  computed: {
    ...mapGetters([
      'isVerified',
      'attachmentsLoaded',
      'optionsLoaded',
      'getStatusText',
      'tagsLoaded',
      'articlesLoaded',
      'articles',
      'hasError'
    ]),
    selected_article() {
      if (this.articlesLoaded) {
        return this.articles.find((a) => a.id == this.$route.params.id);
      }
      return null;
    },
    ready() {
      return this.optionsLoaded && this.articlesLoaded && !!this.copy;
    },
    componentName() {
      return `post-type-${this.copy.post_type}`;
    },
    articleStatusText() {
      return this.getStatusText(this.copy.status);
    },
    canTweet() {
      return this.copy.status === 'publish' && !this.without_update_modified_at;
    },
    canReservation() {
      return this.copy.published_at === null || this.copy.status === 'reservation';
    }
  },
  methods: {
    ...mapActions([
      'fetchOptions',
      'fetchAttachments',
      'fetchArticles',
      'fetchTags',
      'updateArticle'
    ]),
    async handlePreview() {
      this.show_preview = !this.show_preview;
    },
    async handleUpdate() {
      const params = {
        article: this.copy,
        should_tweet: this.should_tweet,
        without_update_modified_at: this.without_update_modified_at,
        preview: false
      };
      await this.updateArticle({ params });

      // 更新が成功すれば遷移ダイアログを無効化してマイページトップへ戻る
      // ステータスが下書きの時は編集画面上部へスクロールする（通知が見えないため）
      // エラーがあれば編集画面上部へスクロールする（通知が見えないため）
      if (!this.hasError) {
        this.unsetUnloadDialog();

        if (!this.isDraft()) {
          this.$router.push({ name: 'index' });
        } else {
          this.scrollToTop();
        }
      } else {
        this.scrollToTop();
      }
    },
    getOriginal() {
      return this.selected_article;
    },
    isDraft() {
      return this.copy.status === 'draft';
    }
  }
};
</script>
<style scoped>
.row {
  max-height: calc(100vh - 2.7rem);
  overflow: hidden;
}

.w-50.col {
  max-height: calc(100vh - 2.7rem);
  overflow: auto;
}
</style>
