<template>
  <div>
    <page-title>新規作成</page-title>
    <div v-if="ready">
      <component :is="component_name" :article="copy">
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
            <b-button @click.prevent="handlePreview"> プレビュー表示 </b-button>
          </fetching-overlay>
          <fetching-overlay>
            <b-button variant="primary" @click.prevent="handleCreate">
              「{{ article_status }}」で保存
            </b-button>
          </fetching-overlay>
        </b-form-group>
      </component>
    </div>
    <loading v-else />
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import { validateVerified } from "../../mixins/auth";
import { editor } from "../../mixins/editor";
import { preview } from "../../mixins/preview";
import { defaultArticle } from "../../mixins/default_values";
export default {
  data() {
    return {
      article: null,
      should_tweet: true,
    };
  },
  mixins: [validateVerified, preview, defaultArticle, editor],
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
  watch: {
    "$route.params.post_type"() {
      this.initDefaultArticle();
    },
  },
  computed: {
    ...mapGetters([
      "isVerified",
      "attachmentsLoaded",
      "tagsLoaded",
      "optionsLoaded",
      "getStatusText",
      "articles",
      "hasError",
    ]),
    ready() {
      return this.$route.params.post_type && this.optionsLoaded && !!this.copy;
    },
    component_name() {
      return `post-type-${this.copy.post_type}`;
    },
    article_status() {
      return this.getStatusText(this.copy.status);
    },
  },
  methods: {
    ...mapActions([
      "fetchOptions",
      "fetchAttachments",
      "fetchTags",
      "createArticle",
    ]),
    initDefaultArticle() {
      this.article = this.createDefaultArticle(this.$route.params.post_type);
      this.setCopy(this.article);
    },
    async handlePreview() {
      const params = {
        article: this.copy,
        should_tweet: this.should_tweet,
        preview: true,
      };
      const html = await this.createArticle({
        params,
        message: null,
      });

      // プレビュー作成が成功すればプレビューウインドウを表示する
      // エラーがあれば画面上部へスクロールする（通知が見えないため）
      if (!this.hasError && html) {
        return this.createPreview(html);
      }
      this.scrollToTop();
    },
    async handleCreate() {
      const params = {
        article: this.copy,
        should_tweet: this.should_tweet,
        preview: false,
      };
      await this.createArticle({ params });

      // 更新が成功していれば遷移ダイアログを無効化してマイページトップへ戻る
      // ステータスが下書きの時は編集画面へ遷移する
      // エラーがあれば編集画面上部へスクロールする（通知が見えないため）
      if (!this.hasError) {
        this.unsetUnloadDialog();
        if (!this.isDraft()) {
          this.$router.push({ name: "index" });
        } else {
          const id = this.articles.find((a) => a.slug === this.copy.slug).id;
          this.$router.push({ name: "editArticle", params: { id } });
        }
      } else {
        this.scrollToTop();
      }
    },
    getOriginal() {
      return this.article;
    },
    isDraft() {
      return this.copy.status === "draft";
    },
  },
};
</script>
