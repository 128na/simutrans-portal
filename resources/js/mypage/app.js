import Vue from 'vue';
import App from './App.vue';
import use from "./use";

import store from './store';

use.boostrap_vue(Vue);
const router = use.vue_router(Vue);
const i18n = use.i18n(Vue);
use.clipboard(Vue);

// components
const files = require.context('./Components', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

new Vue({
  el: '#app',
  render: h => h(App),
  store,
  i18n,
  router
});
