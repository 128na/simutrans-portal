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

mix.sass('resources/sass/front/index.scss', 'public/css/style.css')
  .js('resources/js/script.js', 'public/js/script.js');

mix.sass('resources/sass/mypage/index.scss', 'public/css/mypage.css')
  .js('resources/js/mypage/app.js', 'public/js/mypage.js');


const productionSourceMaps = false;
mix.sourceMaps(productionSourceMaps);

if (mix.inProduction()) {
  mix.version();
}
