<template>
  <b-navbar
    class="fixed-left py-4"
    type="dark"
    variant="primary"
    toggleable="md"
  >
    <b-navbar-brand class="p-0 mb-md-4 mb-0" :href="top_url">
      Simutrans Addon Portal
    </b-navbar-brand>

    <b-navbar-toggle target="global-menu"></b-navbar-toggle>

    <b-collapse id="global-menu" is-nav>
      <b-navbar-nav v-if="is_logged_in">
        <b-nav-item class="active" v-if="is_admin" :href="admin_url">{{
          $t("[admin] Dashboard")
        }}</b-nav-item>
        <b-nav-item class="active" v-if="is_verified" :to="to_profile">
          <b-icon icon="person" class="mr-1" />
          {{ $t("Edit my profile") }}
        </b-nav-item>
        <b-nav-item class="active" v-if="is_verified" :to="to_analytics">
          <b-icon icon="graph-up" class="mr-1" />
          {{ $t("Access Analytics") }}
        </b-nav-item>
        <b-nav-item class="active" v-if="is_verified" :to="to_addon_post">
          <icon-edit-article class="mr-1" />
          {{
            $t("Create {post_type}", { post_type: $t("post_types.addon-post") })
          }}
        </b-nav-item>
        <b-nav-item
          class="active"
          v-if="is_verified"
          :to="to_addon_introduction"
        >
          <icon-edit-article class="mr-1" />
          {{
            $t("Create {post_type}", {
              post_type: $t("post_types.addon-introduction"),
            })
          }}
        </b-nav-item>
        <b-nav-item class="active" v-if="is_verified" :to="to_page">
          <icon-edit-article class="mr-1" />
          {{ $t("Create {post_type}", { post_type: $t("post_types.page") }) }}
        </b-nav-item>
        <b-nav-item class="active" v-if="is_verified" :to="to_markdown">
          <icon-edit-article class="mr-1" />
          {{
            $t("Create {post_type}", { post_type: $t("post_types.markdown") })
          }}
        </b-nav-item>
        <div class="dropdown-divider border-light" />
        <b-nav-item class="active" @click="logout">
          <b-icon icon="box-arrow-right" class="mr-1" />
          {{ $t("Logout") }}
        </b-nav-item>
      </b-navbar-nav>
      <b-navbar-nav v-else>
        <b-nav-item class="active" :to="to_login">{{ $t("Login") }}</b-nav-item>
        <b-nav-item class="active" :to="to_register">{{
          $t("Register")
        }}</b-nav-item>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>
<script>
import { linkable } from "../../mixins";
export default {
  props: ["user"],
  mixins: [linkable],
  computed: {
    is_logged_in() {
      return !!this.user;
    },
    is_verified() {
      return this.is_logged_in && this.user.verified;
    },
    is_admin() {
      return this.is_logged_in && this.user.admin;
    },
    top_url() {
      return `${process.env.MIX_APP_URL}`;
    },
    admin_url() {
      return `${process.env.MIX_APP_URL}/admin`;
    },
  },
  methods: {
    logout() {
      this.$emit("logout");
    },
  },
};
</script>
