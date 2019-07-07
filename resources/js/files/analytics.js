import Vue from 'vue';
import App from '../components/Analytics.vue';

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

const app = new Vue({
    el: '#app-analytics',
    render: h => h(App)
});
