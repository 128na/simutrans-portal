<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{ $t("Status") }}
      </template>
      <b-form-select
        v-model="article.status"
        :options="options.statuses"
        :state="validationState('article.status')"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{ $t("Title") }}
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
            {{ $t("To Slug") }}
          </b-button>
        </b-input-group-append>
      </b-input-group>
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{ $t("Slug") }}
      </template>
      <b-form-input
        type="text"
        v-model="url_decoded_slug"
        :state="validationState('article.slug')"
      />
      <div class="mt-1 text-break">
        URL: https://simutrans.sakura.ne.jp/portal/articles/{{ article.slug }}
      </div>
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("Thumbnail") }}
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
  </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  name: "form-common",
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
