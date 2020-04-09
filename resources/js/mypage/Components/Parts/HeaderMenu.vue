<template>
  <b-navbar toggleable="lg" type="dark" variant="primary">
    <b-container>
      <b-navbar-brand href="#">Simutrans Addon Portal</b-navbar-brand>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav v-if="is_logged_in">
        <!-- Right aligned nav items -->
        <b-navbar-nav class="ml-auto">
          <b-nav-item-dropdown right>
            <!-- Using 'button-content' slot -->
            <template v-slot:button-content>{{ user.name }}</template>
            <b-dropdown-item v-if="user.verified" :to="to_profile">
              <b-icon icon="person" class="mr-1" />
              {{$t('Edit my profile')}}
            </b-dropdown-item>
            <b-dropdown-item :to="to_addon_post">
              <b-iconstack class="mr-1">
                <b-icon icon="file-zip" scale="0.8" shift-v="0" shift-h="-2" />
                <b-icon icon="pencil" scale="0.5" shift-v="5" shift-h="3" />
              </b-iconstack>
              {{
              $t('Create {post_type}', {post_type:$t('post_types.addon-post')})
              }}
            </b-dropdown-item>
            <b-dropdown-item :to="to_addon_introduction">
              <b-iconstack class="mr-1">
                <b-icon icon="file-text" scale="0.8" shift-v="0" shift-h="-2" />
                <b-icon icon="pencil" scale="0.5" shift-v="5" shift-h="3" />
              </b-iconstack>
              {{
              $t('Create {post_type}', {post_type:$t('post_types.addon-introduction')})
              }}
            </b-dropdown-item>
            <b-dropdown-item :to="to_page">
              <b-iconstack class="mr-1">
                <b-icon icon="file-richtext" scale="0.8" shift-v="0" shift-h="-2" />
                <b-icon icon="pencil" scale="0.5" shift-v="5" shift-h="3" />
              </b-iconstack>
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
    }
  },
  methods: {
    setUser() {
      this.$emit("logout");
    }
  }
};
</script>
