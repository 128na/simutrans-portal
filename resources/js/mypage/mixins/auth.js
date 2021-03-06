import { mapGetters } from "vuex";
import store from '../store';
/**
 * 未ログインならログインページへ移動させる
 */
export const validateLogin = {
  // 初期化前はコンポーネント描画されないため、beforeRouteEnterではルートはそのまま、createdでログインチェックをする
  beforeRouteEnter(to, from, next) {
    if (store.getters.initialized === true && store.getters.isLoggedIn === false) {
      return next({ name: "login" });
    }
    next();
  },
  created() {
    if (!this.isLoggedIn) {
      return this.$router.push({ name: "login" });
    }
  },
  watch: {
    isLoggedIn() {
      if (!this.isLoggedIn) {
        return this.$router.push({ name: "login" });
      }
    },
  },
  computed: {
    ...mapGetters(["isLoggedIn"]),
  }
}
/**
 * 未ログインならログインページへ移動させる
 * メール未認証ならマイページトップへ移動させる
 */
export const validateVerified = {
  beforeRouteEnter(to, from, next) {
    if (store.getters.initialized === true) {
      if (store.getters.isLoggedIn === false) {
        return next({ name: "login" });
      }
      if (store.getters.isVerified === false) {
        return next({ name: "index" });
      }
    }
    next();
  },
  created() {
    if (!this.isLoggedIn) {
      return this.$router.push({ name: "login" });
    }
    if (!this.isVerified) {
      return this.$router.push({ name: "index" });
    }
  },
  watch: {
    isVerified() {
      if (!this.isLoggedIn) {
        return this.$router.push({ name: "login" });
      }
      if (!this.isVerified) {
        return this.$router.push({ name: "index" });
      }
    },
  },
  computed: {
    ...mapGetters(["isLoggedIn", "isVerified"]),
  }
}
/**
 * ログイン済みならマイページトップへ移動させる
 */
export const validateGuest = {
  beforeRouteEnter(to, from, next) {
    if (store.getters.initialized === true && store.getters.isLoggedIn === true) {
      return next({ name: "index" });
    }
    next();
  },
  created() {
    if (this.isLoggedIn) {
      this.$router.push({ name: "index" });
    }
  },
  watch: {
    isLoggedIn(val) {
      if (val) {
        this.$router.push({ name: "index" });
      }
    },
  },
  computed: {
    ...mapGetters(["isLoggedIn"]),
  }
}
