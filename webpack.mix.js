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
mix.js('resources/js/front.js', 'public/js')
    .sass('resources/sass/front/index.scss', 'public/css/front.css')
    .version();

mix.js('resources/js/mypage.js', 'public/js')
    .sass('resources/sass/mypage/index.scss', 'public/css/mypage.css')
    .version();

mix.js('resources/js/admin.js', 'public/js')
    .sass('resources/sass/admin/index.scss', 'public/css/admin.css')
    .version();
