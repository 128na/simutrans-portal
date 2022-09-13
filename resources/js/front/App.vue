<template>
  <main class="container-fluid py-4">
    <front-menu :pak_addon_counts="pak_addon_counts" :user_addon_counts="user_addon_counts" />
    <router-view />
  </main>
</template>
<script>
import axios from 'axios';

export default {
  data() {
    return {
      pak_addon_counts: null,
      user_addon_counts: null
    };
  },

  mounted() {
    if (!this.sidebar) {
      this.fetchSidebar();
    }
  },
  methods: {
    async fetchSidebar() {
      const res = await axios.get('/api/v3/front/sidebar');
      if (res.status === 200) {
        this.pak_addon_counts = res.data.pak_addon_counts;
        this.user_addon_counts = res.data.user_addon_counts;
      }
    }
  }
};
</script>
