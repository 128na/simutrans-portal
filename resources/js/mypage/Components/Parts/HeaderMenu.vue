<template>
  <b-navbar toggleable="lg" type="dark" variant="primary">
    <b-container>
      <b-navbar-brand :href="top_url">Simutrans Addon Portal</b-navbar-brand>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav v-if="is_logged_in">
        <!-- Right aligned nav items -->
        <b-navbar-nav class="ml-auto">
          <b-nav-item-dropdown right>
            <!-- Using 'button-content' slot -->
            <template v-slot:button-content>{{ user.name }}</template>
            <b-dropdown-item v-if="user.admin" :href="admin_url">{{$t('[admin] Dashboard')}}</b-dropdown-item>
            <b-dropdown-item v-if="user.verified" :to="to_profile">
              <b-icon icon="person" class="mr-1" />
              {{$t('Edit my profile')}}
            </b-dropdown-item>
            <b-dropdown-item :to="to_addon_post">
              <icon-create-addon-post class="mr-1" />
              {{
              $t('Create {post_type}', {post_type:$t('post_types.addon-post')})
              }}
            </b-dropdown-item>
            <b-dropdown-item :to="to_addon_introduction">
              <icon-create-addon-introduction class="mr-1" />
              {{
              $t('Create {post_type}', {post_type:$t('post_types.addon-introduction')})
              }}
            </b-dropdown-item>
            <b-dropdown-item :to="to_page">
              <icon-create-page class="mr-1" />
              {{
              $t('Create {post_type}', {post_type:$t('post_types.page')})
              }}
            </b-dropdown-item>
            <b-dropdown-divider />
            <b-dropdown-item @click="logout">
              <b-icon icon="box-arrow-right" class="mr-1" />
              {{$t('Logout')}}
            </b-dropdown-item>
          </b-nav-item-dropdown>
        </b-navbar-nav>
      </b-collapse>
      <b-collapse id="nav-collapse" is-nav v-else>
        <b-navbar-nav class="ml-auto">
          <b-nav-item :to="to_login">{{$t('Login')}}</b-nav-item>
          <b-nav-item :to="to_register">{{$t('Register')}}</b-nav-item>
        </b-navbar-nav>
      </b-collapse>
    </b-container>
  </b-navbar>
</template>
<script>
import { api_handlable, linkable } from "../../mixins";
export default {
  props: ["user"],
  mixins: [api_handlable, linkable],
  computed: {
    is_logged_in() {
      return !!this.user;
    },
    top_url() {
      return `${process.env.MIX_APP_URL}`;
    },
    admin_url() {
      return `${process.env.MIX_APP_URL}/admin`;
    }
  },
  methods: {
    setUser() {
      this.$emit("logout");
    }
  }
};
</script>
