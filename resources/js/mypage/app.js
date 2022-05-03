import Vue from 'vue';
import store from './store';
import router from './plugins/router';
import './plugins';

import App from './App.vue';

// components
const files = require.context('./Components', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

if (document.getElementById('app')) {
  new Vue({
    el: '#app',
    render: h => h(App),
    store,
    router
  });
}
