<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        本文
      </template>
      <div
        v-for="(section, index) in article.contents.sections"
        :key="index"
        class="mb-3"
      >
        <b-form-textarea
          v-if="isText(section.type)"
          :value="section.text"
          placeholder="テキスト"
          :state="validationState(`article.contents.sections.${index}.text`)"
          @input="(v) => handleInput(index, v)"
        />
        <b-form-input
          v-if="isUrl(section.type)"
          type="url"
          :value="section.url"
          placeholder="URL"
          :state="validationState(`article.contents.sections.${index}.url`)"
          @input="(v) => handleInput(index, v)"
        />
        <media-manager
          v-if="isImage(section.type)"
          :id="article.id"
          :name="`section-${index}`"
          :value="section.id"
          type="Article"
          :only_image="true"
          :state="validationState(`article.contents.sections.${index}.id`)"
          @input="(v) => handleInput(index, v)"
        />
        <b-form-input
          v-if="isCaption(section.type)"
          type="text"
          :value="section.caption"
          placeholder="見出し"
          :state="validationState(`article.contents.sections.${index}.caption`)"
          @input="(v) => handleInput(index, v)"
        />
        <b-button
          variant="danger"
          size="sm"
          class="mt-1"
          @click="handleRemove(index)"
        >
          項目を削除
        </b-button>
        <validation-message
          :field="`article.contents.sections.${index}.text`"
        />
        <validation-message :field="`article.contents.sections.${index}.url`" />
        <validation-message :field="`article.contents.sections.${index}.id`" />
        <validation-message
          :field="`article.contents.sections.${index}.caption`"
        />
      </div>
      <b-button-group>
        <b-button
          variant="outline-secondary"
          @click="handleAdd('caption')"
        >
          見出しを追加
        </b-button>
        <b-button
          variant="outline-secondary"
          @click="handleAdd('text')"
        >
          テキストを追加
        </b-button>
        <b-button
          variant="outline-secondary"
          @click="handleAdd('url')"
        >
          URLを追加
        </b-button>
        <b-button
          variant="outline-secondary"
          @click="handleAdd('image')"
        >
          画像を追加
        </b-button>
      </b-button-group>
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        カテゴリ
      </template>
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.page"
      />
    </b-form-group>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
  props: ['article'],
  computed: {
    ...mapGetters(['options', 'validationState'])
  },
  methods: {
    isText(type) {
      return type === 'text';
    },

    isUrl(type) {
      return type === 'url';
    },

    isImage(type) {
      return type === 'image';
    },

    isCaption(type) {
      return type === 'caption';
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
        return this.article.contents.sections.push({ type: 'text', text: '' });
      }

      if (this.isUrl(type)) {
        return this.article.contents.sections.push({ type: 'url', url: '' });
      }

      if (this.isImage(type)) {
        return this.article.contents.sections.push({ type: 'image', id: null });
      }

      if (this.isCaption(type)) {
        return this.article.contents.sections.push({
          type: 'caption',
          caption: ''
        });
      }
    },
    handleRemove(index) {
      if (window.confirm('削除してもよろしいですか？')) {
        const sections = [...this.article.contents.sections];
        sections.splice(index, 1);
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
