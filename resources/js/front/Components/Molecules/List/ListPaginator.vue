<template>
  <b-pagination-nav v-if="pagination" :value="pagination.current_page" :link-gen="linkGen"
    :number-of-pages="pagination.last_page" use-router></b-pagination-nav>
</template>
<script>
export default {
  props: {
    pagination: {
      type: Object
    }
  },
  methods: {
    linkGen(pageNum) {
      return pageNum === 1 ? '?' : `?page=${pageNum}`;
    }
  }
};
</script>
