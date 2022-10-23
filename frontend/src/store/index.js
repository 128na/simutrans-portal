import { store } from 'quasar/wrappers';
import { createPinia } from 'pinia';

// import example from './module-example'

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Store instance.
 */

export default store(createPinia());
// export default store((/* { ssrContext } */) => {
//   const Store = createPinia();

//   return Store;
// });
