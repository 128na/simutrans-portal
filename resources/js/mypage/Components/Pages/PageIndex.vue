<template>
  <div>
    <h1>{{ $t("Mypage") }}</h1>
    <div v-if="!isVerified">
      <form-verify :user="user" />
    </div>
    <div v-else>
      <article-table
        :articles="articles"
        :user="user"
        @update:articles="$emit('update:articles', $event)"
      />
    </div>
  </div>
</template>
<script>
import { linkable, validateLogin } from "../../mixins";
import { mapGetters, mapActions } from "vuex";
export default {
  mixins: [linkable, validateLogin],
  computed: {
    ...mapGetters(["isVerified", "statusCode", "user"]),
  },
};
</script>
