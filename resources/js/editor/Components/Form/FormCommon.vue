<template>
  <div>
    <b-form-group label="status">
      <b-form-select v-model="article.status" :options="options.statuses" />
    </b-form-group>
    <b-form-group label="title">
      <b-form-input type="text" v-model="article.title" />
    </b-form-group>
    <b-form-group label="slug">
      <b-form-input type="text" v-model="url_decoded_slug" />
      <small>URL: https://simutrans.sakura.ne.jp/portal/articles/{{ article.slug }}</small>
    </b-form-group>
    <b-form-group label="thumbnail">
      <media-manager
        id="thumbnail"
        v-model="article.contents.thumbnail"
        :attachments="attachments"
        :only_image="true"
        @attachmentsUpdated="handleAttachmentsUpdated"
      />
    </b-form-group>
  </div>
</template>
<script>
import { attachments_updatable } from "../../mixins";
export default {
  name: "form-common",
  props: ["article", "attachments", "options"],
  mixins: [attachments_updatable],
  computed: {
    url_decoded_slug: {
      get() {
        return decodeURI(this.article.slug);
      },
      set(val) {
        const replaced = val.replace(
          /(!|"|#|\$|%|&|\'|\(|\)|\*|\+|,|\/|:|;|<|=|>|\?|@|\[|\\|\]|\^|`|\{|\||\}|\s|\.)+/,
          "-"
        );
        this.article.slug = encodeURI(replaced);
      }
    }
  }
};
</script>
