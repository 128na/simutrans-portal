<template>
  <b-row v-if="ready">
    <b-col>
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
              プレビュー表示
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
    <b-col>
      <article-preview :article="copy" />
    </b-col>
  </b-row>
  <loading-message v-else />
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
import { validateVerified } from '../../mixins/auth';
import { editor } from '../../mixins/editor';
import { preview } from '../../mixins/preview';
export default {
  mixins: [validateVerified, preview, editor],
  data() {
    return {
      should_tweet: false,
      without_update_modified_at: false
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
      const params = {
        article: this.copy,
        should_tweet: this.should_tweet,
        without_update_modified_at: this.without_update_modified_at,
        preview: true
      };
      const html = await this.updateArticle({
        params,
        message: null
      });

      // プレビュー作成が成功すればプレビューウインドウを表示する
      // エラーがあれば画面上部へスクロールする（通知が見えないため）
      if (!this.hasError && html) {
        return this.createPreview(html);
      }
      this.scrollToTop();
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
