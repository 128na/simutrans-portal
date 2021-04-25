import Vue from 'vue';
import '../mypage/plugins/index';

import BulkZipDownloader from '../mypage/Components/Templates/BulkZipDownloader.vue';
Vue.component('bulk-zip-downloader', BulkZipDownloader);
import PageSubTitle from '../mypage/Components/Molecules/PageSubTitle.vue';
Vue.component('page-sub-title', PageSubTitle);
import PageDescription from '../mypage/Components/Molecules/PageDescription.vue';
Vue.component('page-description', PageDescription);

new Vue({
  el: '#app',
});
