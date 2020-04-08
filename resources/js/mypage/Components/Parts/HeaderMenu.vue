<template>
  <div>
    <b-navbar toggleable="lg" type="dark" variant="primary">
      <b-navbar-brand href="#">Simutrans Addon Portal</b-navbar-brand>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav v-if="is_logged_in">
        <!-- Right aligned nav items -->
        <b-navbar-nav class="ml-auto">
          <b-nav-item-dropdown text="Create" right>
            <b-dropdown-item :to="to_addon_post">Addon Post</b-dropdown-item>
            <b-dropdown-item :to="to_addon_introduction">Addon Introduction</b-dropdown-item>
            <b-dropdown-item :to="to_page">Page</b-dropdown-item>
          </b-nav-item-dropdown>

          <b-nav-item-dropdown right>
            <!-- Using 'button-content' slot -->
            <template v-slot:button-content>{{ user.name }}</template>
            <b-dropdown-item v-if="user.verified" :to="to_profile">Edit Profile</b-dropdown-item>
            <b-dropdown-item @click="logout">Log Out</b-dropdown-item>
          </b-nav-item-dropdown>
        </b-navbar-nav>
      </b-collapse>
      <b-collapse id="nav-collapse" is-nav v-else>
        <b-navbar-nav class="ml-auto">
          <b-nav-item :to="to_login">Login</b-nav-item>
          <b-nav-item :to="to_register">Register</b-nav-item>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>
  </div>
</template>
<script>
import { api_handlable, linkable } from "../../mixins";
export default {
  props: ["user"],
  mixins: [api_handlable, linkable],
  computed: {
    is_logged_in() {
      return !!this.user;
    }
  },
  methods: {
    setUser() {
      this.$emit("logout");
    }
  }
};
</script>
