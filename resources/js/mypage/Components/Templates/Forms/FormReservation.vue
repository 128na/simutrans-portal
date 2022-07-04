<template>
  <div>
    <b-form-group v-if="isReservation">
      <template slot="label">
        <badge-required />
        予約投稿日時
      </template>
      <b-form-datepicker
        v-model="date"
        :min="minDate"
        locale="ja-JP"
      />
      <b-form-timepicker
        v-model="time"
        locale="ja-JP"
      />
      <small>
        保存時刻より1時間後を指定できます。公開時刻は1分単位で指定できますが、サーバーの都合で実際に公開されるのは5分刻みの時刻になります。<br>
        また、投稿時には自動ツイートはされません。
      </small>
      <validation-message field="article.published_at" />
    </b-form-group>
  </div>
</template>
<script>
import { DateTime } from 'luxon';
import { mapGetters } from 'vuex';
export default {
  props: ['article'],
  computed: {
    ...mapGetters(['options', 'validationState']),
    isReservation() {
      return this.article.status === 'reservation';
    },
    minDate() {
      return DateTime.now().toISODate();
    },
    date: {
      get() {
        return DateTime.fromISO(this.article.published_at).toISODate();
      },
      set(val) {
        this.article.published_at = DateTime.fromISO(`${val}T${this.time}`).toISO();
      }
    },
    time: {
      get() {
        return DateTime.fromISO(this.article.published_at).toFormat('HH:00:00');
      },
      set(val) {
        this.article.published_at = DateTime.fromISO(`${this.date}T${val}`).toISO();
      }
    }
  }
};
</script>
