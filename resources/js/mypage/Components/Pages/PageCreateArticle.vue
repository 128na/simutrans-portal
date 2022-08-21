<template>
  <b-row v-if="ready">
    <b-col class="w-50">
      <page-title>新規作成</page-title>
      <component :is="componentName" :article="copy">
        <form-status :article="copy" :can-reservation="true" />
        <form-reservation :article="copy" />
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
            公開状態が「公開」のときのみ選べます。
          </template>
        </b-form-group>
        <b-form-group>
          <fetching-overlay>
            <b-button @click.prevent="handlePreview">
              {{ show_preview ? 'プレビュー非表示' : 'プレビュー表示' }}
            </b-button>
          </fetching-overlay>
          <fetching-overlay>
            <b-button variant="primary" @click.prevent="handleCreate">
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
import { defaultArticle } from '../../mixins/default_values';
export default {
  mixins: [validateVerified, defaultArticle, editor],
  data() {
    return {
      article: null,
      should_tweet: true,
      show_preview: true
    };
  },
  watch: {
    '$route.params.post_type'() {
      this.initDefaultArticle();
    }
  },
  created() {
    if (this.isVerified) {
      this.initDefaultArticle();
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
      'tagsLoaded',
      'optionsLoaded',
      'getStatusText',
      'articles',
      'hasError'
    ]),
    ready() {
      return this.$route.params.post_type && this.optionsLoaded && !!this.copy;
    },
    componentName() {
      return `post-type-${this.copy.post_type}`;
    },
    articleStatusText() {
      return this.getStatusText(this.copy.status);
    },
    canTweet() {
      return this.copy.status === 'publish';
    }
  },
  methods: {
    ...mapActions([
      'fetchOptions',
      'fetchAttachments',
      'fetchTags',
      'createArticle'
    ]),
    initDefaultArticle() {
      this.article = this.createDefaultArticle(this.$route.params.post_type);
      this.setCopy(this.article);
    },
    async handlePreview() {
      this.show_preview = !this.show_preview;
    },
    async handleCreate() {
      const params = {
        article: this.copy,
        should_tweet: this.should_tweet,
        preview: false
      };
      await this.createArticle({ params });

      // 更新が成功していれば遷移ダイアログを無効化してマイページトップへ戻る
      // ステータスが下書きの時は編集画面へ遷移する
      // エラーがあれば編集画面上部へスクロールする（通知が見えないため）
      if (!this.hasError) {
        this.unsetUnloadDialog();
        if (!this.isDraft()) {
          this.$router.push({ name: 'index' });
        } else {
          const id = this.articles.find((a) => a.slug === this.copy.slug).id;
          this.$router.push({ name: 'editArticle', params: { id } });
        }
      } else {
        this.scrollToTop();
      }
    },
    getOriginal() {
      return this.article;
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
