import Vue from 'vue';
import App from './App.vue';
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)

const files = require.context('./Components', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

new Vue({
    el: '#app',
    render: h => h(App)
});
