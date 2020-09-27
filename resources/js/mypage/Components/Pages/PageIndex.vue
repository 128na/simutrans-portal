<template>
  <div>
    <h1>{{ $t("Mypage") }}</h1>
    <div v-if="!user.verified">
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
import { linkable } from "../../mixins";
export default {
  props: ["articles", "user"],
  mixins: [linkable],
};
</script>
