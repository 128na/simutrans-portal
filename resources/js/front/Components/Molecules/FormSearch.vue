<template>
  <form>
    <b-input-group>
      <b-form-input v-model="word" type="search" placeholder="検索" required />
      <b-input-group-append>
        <b-button variant="outline-light" type="submit" @click.prevent="handleSearch">検索</b-button>
      </b-input-group-append>
    </b-input-group>
  </form>

</template>
<script>
export default {
  data() {
    return {
      word: '',
    };
  },
  created() {
    this.word = this.$route.query.word || '';
  },
  methods: {
    handleSearch(e) {
      this.$router.push({
        name: 'search',
        query: { word: this.word }
      }).catch(e => { });
    }
  }
}
</script>
