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
          <b-icon icon="house-fill" class="nav-icon" />
          管理者画面
        </b-nav-item>
        <b-nav-item class="active" :to="route_mypage_index">
          <b-icon icon="house-fill" class="nav-icon" />
          マイページ
        </b-nav-item>
        <b-nav-item class="active" v-if="isVerified" :to="route_edit_profile">
          <b-icon icon="person-fill" class="nav-icon" />
          プロフィール
        </b-nav-item>
        <b-nav-item class="active" v-if="isVerified" :to="route_analytics">
          <b-icon icon="graph-up" class="nav-icon" />
          アクセス解析
        </b-nav-item>
        <div class="dropdown-divider border-light" />
        <b-nav-text class="text-white">記事作成</b-nav-text>
        <b-nav-item
          class="active"
          v-if="isVerified"
          :to="route_create_addon_post"
        >
          <b-icon icon="file-earmark-zip-fill" class="nav-icon" />
          アドオン投稿
        </b-nav-item>
        <b-nav-item
          class="active"
          v-if="isVerified"
          :to="route_create_addon_introduction"
        >
          <b-icon icon="file-earmark-text-fill" class="nav-icon" />
          アドオン紹介
        </b-nav-item>
        <b-nav-item class="active" v-if="isVerified" :to="route_create_page">
          <b-icon icon="file-earmark-text-fill" class="nav-icon" />
          一般記事
        </b-nav-item>
        <b-nav-item
          class="active"
          v-if="isVerified"
          :to="route_create_markdown"
        >
          <b-icon icon="file-earmark-text-fill" class="nav-icon" />
          一般記事(markdown)
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
<style lang="scss" scoped>
.nav-icon {
  margin-bottom: 2px;
}
</style>
