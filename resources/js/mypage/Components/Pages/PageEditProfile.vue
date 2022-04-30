<template>
  <div v-if="ready">
    <page-title>プロフィール編集</page-title>
    <page-description>
      自身のプロフィール情報を編集できます。<br>
      パスワードの変更は
      <router-link :to="route_password_reset">
        パスワードのリセット
      </router-link>
      から行えます。
    </page-description>
    <form-profile :user="copy">
      <b-form-group>
        <fetching-overlay>
          <b-button
            variant="primary"
            @click.prevent="handleUpdate"
          >
            保存
          </b-button>
        </fetching-overlay>
      </b-form-group>
    </form-profile>
  </div>
  <loading-message v-else />
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
import { validateVerified } from '../../mixins/auth';
import { editor } from '../../mixins/editor';

export default {
  mixins: [validateVerified, editor],
  created() {
    this.setCopy(this.user);
    if (this.isVerified && !this.attachmentsLoaded) {
      this.fetchAttachments();
    }
  },
  computed: {
    ...mapGetters(['isVerified', 'user', 'attachmentsLoaded', 'hasError']),
    ready() {
      return this.attachmentsLoaded && !!this.copy;
    }
  },
  methods: {
    ...mapActions(['fetchAttachments', 'updateUser']),
    async handleUpdate() {
      // メールアドレスの更新が成功すると未認証となり、画面を追い出されるため離脱警告ダイアログを解除
      this.unsetUnloadDialog();
      await this.updateUser({ user: this.copy });

      // エラーがあれば離脱警告ダイアログを再セット
      if (this.hasError) {
        this.setUnloadDialog();
      }
      // 更新が成功すれば編集画面上部へスクロールする（通知が見えないため）
      this.scrollToTop();
    },
    getOriginal() {
      return this.user;
    }
  }
};
</script>
