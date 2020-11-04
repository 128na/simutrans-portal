<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        Pak
      </template>
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.pak"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        形式
      </template>
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.addon"
      />
    </b-form-group>
    <b-form-group v-show="includes_pak128">
      <template slot="label">
        <badge-optional />
        Pak128用描画位置
      </template>
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.pak128_position"
      />
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
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.license"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        ライセンスその他
      </template>
      <b-form-textarea
        v-model="article.contents.license"
        :state="validationState('article.contents.license')"
      />
      <validation-message field="article.contents.license" />
    </b-form-group>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  name: "form-addon",
  props: ["article"],
  computed: {
    ...mapGetters(["options", "validationState"]),
    pak128_category_id() {
      const pak = this.options.categories.pak;
      return pak.find((c) => c.text == "Pak128").value || null;
    },
    includes_pak128() {
      return this.article.categories.includes(this.pak128_category_id);
    },
  },
};
</script>
