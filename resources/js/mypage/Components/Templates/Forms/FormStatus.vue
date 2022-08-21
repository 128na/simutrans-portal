<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        公開状態
      </template>
      <b-form-radio-group v-model="article.status" :options="statuses" button-variant="outline-primary" buttons
        @change="handleChange"></b-form-radio-group>
      <validation-message field="article.status" />
    </b-form-group>
  </div>
</template>
<script>
import { DateTime } from 'luxon';
import { mapGetters } from 'vuex';
export default {
  props: ['article', 'canReservation'],
  computed: {
    ...mapGetters(['options', 'validationState']),
    statuses() {
      return this.canReservation
        ? this.options.statuses
        : this.options.statuses.filter(s => s.value !== 'reservation');
    }
  },
  methods: {
    handleChange(selected) {
      if (selected === 'reservation') {
        this.article.published_at = DateTime.now().plus({ hours: 1 }).toISO();
      } else {
        this.article.published_at = null;
      }
    }
  }
};
</script>
