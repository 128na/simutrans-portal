<template>
  <div v-if="ready">
    <button-back />
    <h1>{{ $t("Edit my profile") }}</h1>
    <form-profile :user="copy">
      <b-form-group>
        <fetching-overlay>
          <b-btn variant="primary" @click="handleUpdate">
            {{ $t("Save") }}
          </b-btn>
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
      this.$store.dispatch("fetchAttachments");
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
      await this.$store.dispatch("updateUser", { user: this.copy });

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
