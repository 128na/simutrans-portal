import Vue from 'vue';
import App from './App.vue';

// boostrap
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue';
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);

// router
import VueRouter from 'vue-router';
Vue.use(VueRouter);

import routes from './routes';
const router = new VueRouter({
  base: '/mypage',
  // mode: 'history',
  scrollBehavior(to, from, savedPosition) {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        resolve(savedPosition
          ? savedPosition
          : { x: 0, y: 0 }
        )
      }, 100) // transitionの時間と合わせる
    })
  },
  routes,
});

// i18n
import VueInternationalization from 'vue-i18n';
import Locale from '../vue-i18n-locales.generated';
Vue.use(VueInternationalization);

const lang = document.documentElement.lang.substr(0, 2);
const i18n = new VueInternationalization({
  locale: lang,
  messages: Locale,
  silentTranslationWarn: true
});

// components
const files = require.context('./Components', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

new Vue({
  el: '#app',
  render: h => h(App),
  i18n,
  router
});
