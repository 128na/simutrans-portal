<template>
  <div>
    <page-title>ログイン</page-title>
    <form-login :params="params">
      <b-form-group>
        <fetching-overlay>
          <b-button
            type="submit"
            variant="primary"
            @click.prevent="handleLogin"
          >
            ログイン
          </b-button>
        </fetching-overlay>
        <a href="/registration_orders/create" class="mx-2">新規登録</a>
        <!-- <router-link :to="route_register" class="mx-2">新規登録</router-link> -->
        |
        <router-link :to="route_password_reset" class="mx-2">
          パスワード再設定
        </router-link>
      </b-form-group>
    </form-login>
    <hr />
    <div class="mb-3" v-if="can_twitter_login">
      <a :href="twitter_login_url" title="Twitterログイン">
        <img src="/storage/default/login_twitter.png" />
      </a>
    </div>
    <div class="mb-3" v-if="can_google_login">
      <a :href="google_login_url" title="Googleログイン">
        <img src="/storage/default/login_google.png" />
      </a>
    </div>
  </div>
</template>
<script>
import { validateGuest } from "../../mixins/auth";
import { mapActions } from "vuex";
export default {
  mixins: [validateGuest],
  data() {
    return {
      params: {
        email: "",
        password: "",
        remember: false,
      },
    };
  },
  methods: {
    ...mapActions(["login"]),
    handleLogin() {
      this.login(this.params);
    },
  },
};
</script>
