<template>
  <div>
    <h1>{{$t('Mypage')}}</h1>
    <div v-if="!user.verified">
      <form-verify :user="user" />
    </div>
    <div>
      <p>
        <b-button variant="primary" :to="to_analytics">
          <b-icon icon="graph-up" class="mr-1" />
          {{$t('Access Analytics')}}
        </b-button>
      </p>
    </div>
    <article-table v-show="articles.length" :articles="articles" :user="user" />
  </div>
</template>
<script>
import { linkable } from "../../mixins";
export default {
  props: ["articles", "user"],
  mixins: [linkable]
};
</script>
