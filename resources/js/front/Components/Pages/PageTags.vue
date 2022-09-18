<template>
  <div>
    <h2>タグ一覧</h2>
    <b-button v-for="t in tags" :key="t.id" :to="toTag(t)" variant="outline-secondary" size="md" class="m-2">
      {{ t.name }} ({{t.count}})
    </b-button>
  </div>
</template>
<script>
import axios from 'axios';
import { watchAndFetch, routeLink } from '../../mixins';
export default {
  mixins: [routeLink, watchAndFetch],
  data() {
    return { tags: [] };
  },
  methods: {
    async fetch() {
      try {
        const res = await axios.get('/api/v3/front/tags');
        if (res.status === 200) {
          this.tags = res.data.data;
        }
      } catch (err) {
        this.handleError(err);
      }
    }
  }
};
</script>
