import Vue from 'vue';
import store from './store';
import router from './plugins/router'
import './plugins';

// components
const files = require.context('./Components', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

import App from './App.vue';
new Vue({
  el: '#app',
  render: h => h(App),
  store,
  router
});
