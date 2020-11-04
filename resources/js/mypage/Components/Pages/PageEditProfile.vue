<template>
  <div v-if="ready">
    <page-title>プロフィール編集</page-title>
    <page-description>
      自身のプロフィール情報を編集できます。<br />
      パスワードの変更は
      <router-link :to="route_password_reset">パスワードのリセット</router-link>
      から行えます。
    </page-description>
    <form-profile :user="copy">
      <b-form-group>
        <fetching-overlay>
          <b-button variant="primary" @click="handleUpdate"> 保存 </b-button>
        </fetching-overlay>
      </b-form-group>
    </form-profile>
  </div>
  <loading v-else />
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import { validateVerified } from "../../mixins/auth";
import { editor } from "../../mixins/editor";

export default {
  mixins: [validateVerified, editor],
  created() {
    this.setCopy(this.user);
    if (!this.attachmentsLoaded) {
      this.fetchAttachments();
    }
  },
  computed: {
    ...mapGetters(["user", "attachmentsLoaded"]),
    ready() {
      return this.attachmentsLoaded && !!this.copy;
    },
  },
  methods: {
    ...mapActions(["fetchAttachments", "updateUser"]),
    async handleUpdate() {
      await this.updateUser({ user: this.copy });

      // 更新が成功すれば遷移ダイアログを無効化して編集画面上部へスクロールする（通知が見えないため）
      if (!this.hasError) {
        this.unsetUnloadDialog();
      }
      this.scrollToTop();
    },
    getOriginal() {
      return this.user;
    },
  },
};
</script>
