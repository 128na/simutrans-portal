<template>
  <b-alert
    show
    variant="info"
  >
    <strong>メールアドレスの確認が済んでいません</strong>
    <p>
      全ての機能を使用するにはメールアドレスの確認が必要です。<br>
      確認メールを再送信するには下のボタンをクリックしてください。
    </p>
    <b-button
      variant="secondary"
      @click.prevent="resend"
    >
      確認メールを再送信する
    </b-button>
  </b-alert>
</template>
<script>
import { mapActions } from 'vuex';
export default {
  methods: {
    ...mapActions(['sendVerifyEmail']),
    resend() {
      this.sendVerifyEmail();
    }
  }
};
</script>
