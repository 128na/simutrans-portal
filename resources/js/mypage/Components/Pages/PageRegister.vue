<template>
  <div>
    <page-title>新規登録</page-title>
    <form-register :params="params">
      <b-form-group>
        <div class="mb-4" v-if="can_register">
          現在、一部環境からの新規登録を遮断しています。<br />
          制限によって登録ができない場合は
          <a
            href="https://twitter.com/128Na"
            target="_blank"
            rel="noopener noreferrer"
          >
            @128Na
          </a>
          までお問い合わせください。<br />
          ※Twitterのクオリティフィルターにより除外されるアカウント（未認証など）からの問い合わせは受信されませんのでご注意ください。
        </div>
        <fetching-overlay>
          <b-button
            variant="primary"
            type="submit"
            @click.prevent="handleRegister"
          >
            新規登録
          </b-button>
        </fetching-overlay>
      </b-form-group>
    </form-register>
  </div>
</template>
<script>
import { validateGuest } from "../../mixins/auth";
import { mapActions } from "vuex";
export default {
  mixins: [validateGuest],
  props: ["user"],
  data() {
    return {
      params: {
        name: "",
        email: "",
        password: "",
      },
    };
  },
  methods: {
    ...mapActions(["register"]),
    handleRegister() {
      this.register(this.params);
    },
  },
};
</script>
