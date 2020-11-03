<template>
  <b-navbar
    class="fixed-left py-4"
    type="dark"
    variant="primary"
    toggleable="lg"
  >
    <b-navbar-brand class="p-0 mb-lg mb-2 mb-0" :href="top_url">
      Simutrans Addon Portal
    </b-navbar-brand>

    <b-navbar-toggle target="global-menu"></b-navbar-toggle>

    <b-collapse id="global-menu" is-nav>
      <b-navbar-nav v-if="isLoggedIn">
        <b-nav-item class="active" v-if="isAdmin" :href="admin_url">
          管理者画面
        </b-nav-item>
        <b-nav-item class="active" :to="route_mypage_index">
          マイページトップ
        </b-nav-item>
        <b-nav-item class="active" v-if="isVerified" :to="route_edit_profile">
          プロフィール編集
        </b-nav-item>
        <b-nav-item class="active" v-if="isVerified" :to="route_analytics">
          アクセス解析
        </b-nav-item>
        <b-nav-item
          class="active"
          v-if="isVerified"
          :to="route_create_addon_post"
        >
          アドオン投稿を作成
        </b-nav-item>
        <b-nav-item
          class="active"
          v-if="isVerified"
          :to="route_create_addon_introduction"
        >
          アドオン紹介を作成
        </b-nav-item>
        <b-nav-item class="active" v-if="isVerified" :to="route_create_page">
          一般記事を作成
        </b-nav-item>
        <b-nav-item
          class="active"
          v-if="isVerified"
          :to="route_create_markdown"
        >
          一般記事(markdown)を作成
        </b-nav-item>
        <div class="dropdown-divider border-light" />
        <b-nav-item class="active" @click="logout">ログアウト</b-nav-item>
      </b-navbar-nav>
      <b-navbar-nav v-else>
        <b-nav-item class="active" :to="route_login">ログイン</b-nav-item>
        <b-nav-item class="active" :to="route_register">新規登録</b-nav-item>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
export default {
  computed: {
    ...mapGetters(["isLoggedIn", "isVerified", "isAdmin"]),
    top_url() {
      return `${process.env.MIX_APP_URL}`;
    },
    admin_url() {
      return `${process.env.MIX_APP_URL}/admin`;
    },
  },
  methods: {
    ...mapActions(["logout"]),
  },
};
</script>
