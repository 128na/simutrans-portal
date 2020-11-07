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
        <b-nav-item v-if="isAdmin" :href="admin_url">
          <b-icon icon="house-fill" class="nav-icon" />
          管理者画面
        </b-nav-item>
        <b-nav-item :to="route_mypage_index" :active="isActive('index')">
          <b-icon icon="house-fill" class="nav-icon" />
          マイページ
        </b-nav-item>
        <b-nav-item
          v-if="isVerified"
          :to="route_edit_profile"
          :active="isActive('editProfile')"
        >
          <b-icon icon="person-fill" class="nav-icon" />
          プロフィール
        </b-nav-item>
        <b-nav-item
          v-if="isVerified"
          :to="route_analytics"
          :active="isActive('analyticsArticle')"
        >
          <b-icon icon="graph-up" class="nav-icon" />
          アクセス解析
        </b-nav-item>
        <b-nav-text class="" v-if="isVerified">記事作成</b-nav-text>
        <b-nav-item
          v-if="isVerified"
          :to="route_create_addon_post"
          :active="isActive('createArticle', 'addon-post')"
        >
          <b-icon icon="file-earmark-zip-fill" class="nav-icon" />
          アドオン投稿
        </b-nav-item>
        <b-nav-item
          v-if="isVerified"
          :to="route_create_addon_introduction"
          :active="isActive('createArticle', 'addon-introduction')"
        >
          <b-icon icon="file-earmark-text-fill" class="nav-icon" />
          アドオン紹介
        </b-nav-item>
        <b-nav-item
          v-if="isVerified"
          :to="route_create_page"
          :active="isActive('createArticle', 'page')"
        >
          <b-icon icon="file-earmark-text-fill" class="nav-icon" />
          一般記事
        </b-nav-item>
        <b-nav-item
          v-if="isVerified"
          :to="route_create_markdown"
          :active="isActive('createArticle', 'markdown')"
        >
          <b-icon icon="file-earmark-text-fill" class="nav-icon" />
          一般記事(markdown)
        </b-nav-item>
        <b-nav-item :to="route_logout">ログアウト</b-nav-item>
      </b-navbar-nav>
      <b-navbar-nav v-else>
        <b-nav-item :to="route_login" :active="isActive('login')">
          ログイン
        </b-nav-item>
        <b-nav-item :to="route_register" :active="isActive('register')">
          新規登録
        </b-nav-item>
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
<style lang="scss" scoped>
.nav-icon {
  margin-bottom: 2px;
}
#global-menu {
  .nav-link,
  .navbar-text {
    color: rgba(255, 255, 255, 1);
    margin: 0 -0.9rem;
    padding: 0.5rem 1.4rem;
  }
  .nav-link {
    &:hover {
      background-color: rgba(0, 0, 0, 0.1);
    }
    &.active {
      background-color: rgba(0, 0, 0, 0.2);
    }
  }
}
</style>
