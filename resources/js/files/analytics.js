import Vue from 'vue';
import App from '../components/Analytics.vue';
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

const app_selector = 'app-analytics';
if (document.getElementById(app_selector)) {
    const app = new Vue({
        el: `#${app_selector}`,
        render: h => h(App)
    });
}
