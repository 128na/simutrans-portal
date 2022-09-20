<template>
  <div class="card profile-card mt-4">
    <div>
      <img :src="profile.avatar_url" class="rounded">
    </div>
    <div class="card-body">
      <h5 class="card-title">{{ profile.name }}</h5>
      <p class="card-text">
        {{ profile.description || '説明がありません' }}
      </p>
      <div v-if="profile.twitter">
        <a :href="twitterUrl" target="_blank" rel="noopener nofollow">&#064;{{ profile.twitter }}</a>
      </div>
      <div v-if="profile.website">
        <a :href="profile.website" target="_blank" rel="noopener nofollow">{{profile.website }}</a>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    profile: {
      type: Object,
      required: true
    }
  },
  computed: {
    twitterUrl() {
      return `https://twitter.com/${this.profile.twitter}`;
    }
  }
};
</script>
