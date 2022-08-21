<template>
  <div>
    <page-title>パスワード再設定</page-title>
    <page-description>
      指定したメールアドレスにパスワード再設定用のリンクを送信します。<br>
      送信されたリンクのページページから再設定ができます。
    </page-description>
    <form-reset :params="params">
      <b-form-group>
        <fetching-overlay>
          <b-button variant="primary" type="submit" @click.prevent="handleSubmit">
            再設定用のメールを送信
          </b-button>
        </fetching-overlay>
      </b-form-group>
    </form-reset>
  </div>
</template>
<script>
import { mapActions } from 'vuex';
export default {
  data() {
    return {
      params: {
        email: ''
      }
    };
  },
  methods: {
    ...mapActions(['sendResetEmail']),
    handleSubmit() {
      this.sendResetEmail(this.params);
    }
  }
};
</script>
