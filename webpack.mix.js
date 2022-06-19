const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .sass('resources/sass/front/index.scss', 'public/css/front.css')
  .sass('resources/sass/mypage/index.scss', 'public/css/mypage.css')
  .sass('resources/sass/admin/index.scss', 'public/css/admin.css')
  .js('resources/js/front/index.js', 'public/js/front.js')
  .js('resources/js/mypage/app.js', 'public/js/mypage.js')
  .js('resources/js/admin/app.js', 'public/js/admin.js')
  .vue({ version: 2 })
  .extract();

if (mix.inProduction()) {
  mix.version();
} else {
  mix.sourceMaps();
}
