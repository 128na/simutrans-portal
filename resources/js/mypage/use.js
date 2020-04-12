import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue';
import VueRouter from 'vue-router';
import VueInternationalization from 'vue-i18n';
import VueClipboard from 'vue-clipboard2';
import routes from './routes';
import Locale from '../vue-i18n-locales.generated';

export default {
  boostrap_vue(Vue) {
    Vue.use(BootstrapVue);
    Vue.use(BootstrapVueIcons);
  },
  vue_router(Vue) {
    // router
    Vue.use(VueRouter);
    return new VueRouter({
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
  },
  i18n(Vue) {
    Vue.use(VueInternationalization);

    const lang = document.documentElement.lang.substr(0, 2);
    return new VueInternationalization({
      locale: lang,
      messages: Locale,
      silentTranslationWarn: true
    });
  },
  clipboard(Vue) {
    Vue.use(VueClipboard);
  }
};
