<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        公開状態
      </template>
      <b-form-select
        v-model="article.status"
        :options="options.statuses"
        :state="validationState('article.status')"
      />
      <validation-message field="article.status" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        タイトル
      </template>
      <b-input-group>
        <b-form-input
          type="text"
          v-model="article.title"
          :state="validationState('article.title')"
        />
        <b-input-group-append>
          <b-button @click="handleSlug">
            <b-icon icon="arrow-down" />
            タイトルをパーマリンクにコピー
          </b-button>
        </b-input-group-append>
      </b-input-group>
      <validation-message field="article.title" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        パーマリンク
      </template>
      <b-form-input
        type="text"
        v-model="url_decoded_slug"
        :state="validationState('article.slug')"
      />
      <div class="mt-1 text-break">
        URL: {{ base_url }}/articles/{{ article.slug }}
      </div>
      <validation-message field="article.slug" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        サムネイル画像
      </template>
      <media-manager
        name="thumbnail"
        v-model="article.contents.thumbnail"
        type="Article"
        :id="article.id"
        :only_image="true"
        :state="validationState('article.contents.thumbnail')"
      />
    </b-form-group>
    <validation-message field="article.contents.thumbnail" />
  </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  props: ["article"],
  computed: {
    ...mapGetters(["options", "validationState"]),
    url_decoded_slug: {
      get() {
        return decodeURI(this.article.slug);
      },
      set(val) {
        const replaced = val
          .toLowerCase()
          .replace(
            /(!|"|#|\$|%|&|\'|\(|\)|\*|\+|,|\/|:|;|<|=|>|\?|@|\[|\\|\]|\^|`|\{|\||\}|\s|\.)+/gi,
            "-"
          );
        this.article.slug = encodeURI(replaced);
      },
    },
  },
  methods: {
    handleSlug() {
      this.url_decoded_slug = this.article.title;
    },
  },
};
</script>
