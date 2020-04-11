<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{$t('Sections')}}
      </template>
      <b-form inline v-for="(section,index) in article.contents.sections" :key="index">
        <b-form-textarea
          v-if="isText(section.type)"
          :value="section.text"
          :placeholder="$t('Text')"
          :state="state(`article.contents.sections.${index}.text`)"
          @input="v=>handleInput(index, v)"
        />
        <b-form-input
          v-if="isUrl(section.type)"
          type="url"
          :value="section.url"
          placeholder="URL"
          :state="state(`article.contents.sections.${index}.url`)"
          @input="v=>handleInput(index, v)"
        />
        <media-manager
          v-if="isImage(section.type)"
          :name="`section-${index}`"
          :value="section.id"
          type="Article"
          :id="article.id"
          :attachments="attachments"
          :only_image="true"
          :state="state(`article.contents.sections.${index}.id`)"
          @input="v=>handleInput(index, v)"
          @update:attachments="$emit('update:attachments', $event)"
        />
        <b-form-input
          v-if="isCaption(section.type)"
          type="text"
          :value="section.caption"
          :placeholder="$t('Caption')"
          :state="state(`article.contents.sections.${index}.caption`)"
          @input="v=>handleInput(index, v)"
        />
        <b-button variant="outline-danger" @click="handleRemove(index)" size="sm" pill>&times;</b-button>
      </b-form>
      <b-btn @click="handleAdd('caption')">{{$t('Add Caption')}}</b-btn>
      <b-btn @click="handleAdd('text')">{{$t('Add Text')}}</b-btn>
      <b-btn @click="handleAdd('url')">{{$t('Add URL')}}</b-btn>
      <b-btn @click="handleAdd('image')">{{$t('Add Image')}}</b-btn>
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{$t('Categories')}}
      </template>
      <b-form-checkbox-group v-model="article.categories" :options="options.categories.page" />
    </b-form-group>
  </div>
</template>
<script>
import { validatable } from "../../mixins";
export default {
  name: "form-page",
  props: ["article", "attachments", "options"],
  mixins: [validatable],
  methods: {
    isText(type) {
      return type === "text";
    },

    isUrl(type) {
      return type === "url";
    },

    isImage(type) {
      return type === "image";
    },

    isCaption(type) {
      return type === "caption";
    },
    handleInput(index, value) {
      const sections = [...this.article.contents.sections];
      const section = sections.splice(index, 1)[0];
      if (this.isText(section.type)) {
        section.text = value;
      }

      if (this.isUrl(section.type)) {
        section.url = value;
      }

      if (this.isImage(section.type)) {
        section.id = value;
      }

      if (this.isCaption(section.type)) {
        section.caption = value;
      }
      this.article.contents.sections.splice(index, 1, section);
    },
    handleAdd(type) {
      if (this.isText(type)) {
        return this.article.contents.sections.push({ type: "text", text: "" });
      }

      if (this.isUrl(type)) {
        return this.article.contents.sections.push({ type: "url", url: "" });
      }

      if (this.isImage(type)) {
        return this.article.contents.sections.push({ type: "image", id: null });
      }

      if (this.isCaption(type)) {
        return this.article.contents.sections.push({
          type: "caption",
          caption: ""
        });
      }
    },
    handleRemove(index) {
      if (window.confirm(this.$t("Are you sure you want to delete?"))) {
        const sections = [...this.article.contents.sections];
        sections.splice(index, 1)[0];
        this.article.contents.sections = sections;
      }
    }
  }
};
</script>
<style lang="scss" scoped>
.form-inline {
  margin: 1rem 0;
  justify-content: space-between;
  align-items: flex-start;
  input,
  textarea {
    flex: 1;
    margin-right: 1rem;
  }
}
</style>
