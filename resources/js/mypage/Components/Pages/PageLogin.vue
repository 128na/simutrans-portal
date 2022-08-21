<template>
  <div>
    <page-title>ログイン</page-title>
    <form-login :params="params">
      <b-form-group>
        <fetching-overlay>
          <b-button type="submit" variant="primary" @click.prevent="handleLogin">
            ログイン
          </b-button>
        </fetching-overlay>
        <router-link :to="route_password_reset" class="mx-2">
          パスワード再設定
        </router-link>
      </b-form-group>
    </form-login>
  </div>
</template>
<script>
import { validateGuest } from '../../mixins/auth';
import { mapActions } from 'vuex';
export default {
  mixins: [validateGuest],
  data() {
    return {
      params: {
        email: '',
        password: '',
        remember: false
      }
    };
  },
  methods: {
    ...mapActions(['login']),
    handleLogin() {
      this.login(this.params);
    }
  }
};
</script>
