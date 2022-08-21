<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        Pak
      </template>
      <b-form-checkbox-group v-model="selectedCategories" :options="pak" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        形式
      </template>
      <b-form-checkbox-group v-model="selectedCategories" :options="addon" />
    </b-form-group>
    <b-form-group v-show="includes_pak128">
      <template slot="label">
        <badge-optional />
        Pak128用描画位置
      </template>
      <b-form-checkbox-group v-model="selectedCategories" :options="pak128_position" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        タグ
      </template>
      <tag-selector v-model="article.tags" :creatable="true" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        ライセンス
      </template>
      <b-form-checkbox-group v-model="selectedCategories" :options="license" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        ライセンスその他
      </template>
      <countable-textarea v-model="article.contents.license" :max-length="2048"
        :state="validationState('article.contents.license')" />
      <validation-message field="article.contents.license" />
    </b-form-group>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
  props: ['article'],
  computed: {
    ...mapGetters(['options', 'validationState', 'getCategory']),
    pak128_category_id() {
      const pak = this.options.categories.pak;
      return pak.find((c) => c.name == 'Pak128').id || null;
    },
    includes_pak128() {
      return this.article.categories.some(c => c.id === this.pak128_category_id);
    },
    pak() {
      return this.options.categories.pak.map(c => Object.create({ text: c.name, value: c.id }));
    },
    addon() {
      return this.options.categories.addon.map(c => Object.create({ text: c.name, value: c.id }));
    },
    pak128_position() {
      return this.options.categories.pak128_position.map(c => Object.create({ text: c.name, value: c.id }));
    },
    license() {
      return this.options.categories.license.map(c => Object.create({ text: c.name, value: c.id }));
    },
    selectedCategories:
    {
      get() {
        return this.article.categories.map(c => c.id);
      },
      set(v) {
        this.article.categories = v.map(c => this.getCategory(c));
      }
    }
  }
};
</script>
