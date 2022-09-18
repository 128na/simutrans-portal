<template>
  <div class="articles">
    <article class="mb-4" v-for="a in articles" :key="a.id">
      <div class="article-header border-bottom mb-1">
        <router-link :to="{name:'show', params:{slug:a.slug}}">
          <strong>{{ a.title }}</strong>
        </router-link>
        <div class="article-main d-flex mb-1">
          <router-link :to="{name:'show', params:{slug:a.slug}}">
            <list-thumbnail :article="a" />
          </router-link>
          <span class="pl-2 article-description">
            {{ a.contents.description }}
          </span>
        </div>
      </div>
    </article>
  </div>
</template>
<script>
export default {
  props: {
    articles: {
      type: Array,
      required: false
    }
  }
};
</script>
