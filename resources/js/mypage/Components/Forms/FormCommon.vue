<template>
  <div>
    <b-form-group :label="$t('Status')">
      <b-form-select v-model="article.status" :options="options.statuses" />
    </b-form-group>
    <b-form-group :label="$t('Title')">
      <b-input-group>
        <b-form-input type="text" v-model="article.title" />
        <b-input-group-append>
          <b-button @click="handleSlug">
            <b-icon icon="arrow-down" />
            {{$t('To Slug')}}
          </b-button>
        </b-input-group-append>
      </b-input-group>
    </b-form-group>
    <b-form-group :label="$t('Slug')">
      <b-form-input type="text" v-model="url_decoded_slug" />
      <div class="mt-1">URL: https://simutrans.sakura.ne.jp/portal/articles/{{ article.slug }}</div>
    </b-form-group>
    <b-form-group :label="$t('Thumbnail')">
      <media-manager
        name="thumbnail"
        v-model="article.contents.thumbnail"
        type="Article"
        :id="article.id"
        :attachments="attachments"
        :only_image="true"
        @update:attachments="$emit('update:attachments', $event)"
      />
    </b-form-group>
  </div>
</template>
<script>
export default {
  name: "form-common",
  props: ["article", "attachments", "options"],
  computed: {
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
      }
    }
  },
  methods: {
    handleSlug() {
      this.url_decoded_slug = this.article.title;
    }
  }
};
</script>
