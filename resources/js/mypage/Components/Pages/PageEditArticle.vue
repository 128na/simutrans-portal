<template>
  <div>
    <page-title>編集</page-title>
    <div v-if="ready">
      <component
        :is="component_name"
        :article="copy"
      >
        <b-form-group>
          <template slot="label">
            <badge-optional />
            自動ツイート
          </template>
          <b-form-checkbox v-model="should_tweet">
            記事公開時にツイートする
          </b-form-checkbox>
        </b-form-group>
        <b-form-group>
          <fetching-overlay>
            <b-button @click.prevent="handlePreview">
              プレビュー表示
            </b-button>
          </fetching-overlay>
          <fetching-overlay>
            <b-button
              variant="primary"
              @click.prevent="handleUpdate"
            >
              「{{ article_status }}」で保存
            </b-button>
          </fetching-overlay>
        </b-form-group>
      </component>
    </div>
    <loading-message v-else />
  </div>
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
      should_tweet: false
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
    component_name() {
      return `post-type-${this.copy.post_type}`;
    },
    article_status() {
      return this.getStatusText(this.copy.status);
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
