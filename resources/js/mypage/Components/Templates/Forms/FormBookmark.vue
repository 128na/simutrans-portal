<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        タイトル
      </template>
      <b-form-input
        type="text"
        v-model="bookmark.title"
        :state="validationState('bookmark.title')"
      />
      <validation-message field="bookmark.title" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        公開設定
      </template>
      <b-form-checkbox
        v-model="bookmark.is_public"
        :state="validationState('bookmark.is_public')"
      >
        公開する
      </b-form-checkbox>
      <validation-message field="bookmark.is_public" />
      <small>
        公開すると公開ブックマーク一覧に表示されるようになります。
      </small>
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        説明
      </template>
      <b-form-textarea
        v-model="bookmark.description"
        :state="validationState('bookmark.description')"
      />
      <validation-message field="bookmark.description" />
    </b-form-group>
    <form-bookmark-items v-model="bookmark.bookmarkItems" />
    <slot />
  </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  props: ["bookmark"],
  computed: {
    ...mapGetters(["validationState"]),
  },
};
</script>
