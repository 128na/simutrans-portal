import Vue from 'vue';
import VueClipboard from 'vue-clipboard2';
Vue.use(VueClipboard);


import route_shortcut from '../mixins/route';
Vue.mixin(route_shortcut);

import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue';
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);
