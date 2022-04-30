import Vue from 'vue';
import '../mypage/plugins/index';

import BulkZipDownloader from '../mypage/Components/Templates/BulkZipDownloader.vue';
import PageSubTitle from '../mypage/Components/Molecules/PageSubTitle.vue';
import PageDescription from '../mypage/Components/Molecules/PageDescription.vue';
Vue.component('BulkZipDownloader', BulkZipDownloader);
Vue.component('PageSubTitle', PageSubTitle);
Vue.component('PageDescription', PageDescription);

new Vue({
  el: '#app'
});
