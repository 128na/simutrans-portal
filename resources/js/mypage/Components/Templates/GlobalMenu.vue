<template>
  <b-navbar
    class="fixed-left py-2 py-lg-4"
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
        <b-nav-item v-if="isAdmin" :href="admin_url">
          <b-icon icon="house-fill" class="nav-icon" />
          管理者画面
        </b-nav-item>
        <nav-link :to="route_mypage_index">
          <b-icon icon="house-fill" class="nav-icon" />
          マイページ
        </nav-link>
        <nav-link v-if="isVerified" :to="route_edit_profile">
          <b-icon icon="person-fill" class="nav-icon" />
          プロフィール
        </nav-link>
        <nav-link v-if="isVerified" :to="route_analytics">
          <b-icon icon="graph-up" class="nav-icon" />
          アクセス解析
        </nav-link>
        <nav-link v-if="isVerified" :to="route_bookmarks">
          <b-icon icon="bookmark-star-fill" class="nav-icon" />
          ブックマーク
        </nav-link>
        <nav-link v-if="isVerified" :to="route_tokens">
          <b-icon icon="key-fill" class="nav-icon" />
          認証管理
        </nav-link>
        <b-nav-text class="" v-if="isVerified">記事作成</b-nav-text>
        <nav-link v-if="isVerified" :to="route_create_addon_post">
          <b-icon icon="file-earmark-zip-fill" class="nav-icon" />
          アドオン投稿
        </nav-link>
        <nav-link v-if="isVerified" :to="route_create_addon_introduction">
          <b-icon icon="file-earmark-text-fill" class="nav-icon" />
          アドオン紹介
        </nav-link>
        <nav-link v-if="isVerified" :to="route_create_page">
          <b-icon icon="file-earmark-text-fill" class="nav-icon" />
          一般記事
        </nav-link>
        <nav-link v-if="isVerified" :to="route_create_markdown">
          <b-icon icon="file-earmark-text-fill" class="nav-icon" />
          一般記事(markdown)
        </nav-link>
        <nav-link :to="route_logout">ログアウト</nav-link>
      </b-navbar-nav>
      <b-navbar-nav v-else>
        <nav-link :to="route_login">ログイン</nav-link>
        <b-nav-item href="/registration_orders/create">新規登録</b-nav-item>
        <!-- <nav-link :to="route_register"> 新規登録 </nav-link> -->
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
export default {
  computed: {
    ...mapGetters(["isLoggedIn", "isVerified", "isAdmin"]),
  },
  methods: {
    ...mapActions(["logout"]),
    isActive(name, post_type = null) {
      return post_type
        ? this.$route.name === name &&
            this.$route.params.post_type === post_type
        : this.$route.name === name;
    },
  },
};
</script>
