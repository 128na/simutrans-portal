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

const fs = require('fs');
const path = require('path');

const replaces = [
  { key: '\\js\\vendor.', dist: '\\js\\vendor.js' },
  { key: '\\js\\app.', dist: '\\js\\app.js' },
  { key: '\\css\\app.', dist: '\\css\\app.css' },
  { key: '\\css\\vendor.', dist: '\\css\\vendor.css' }
];

/**
 * Quasarのバージョニングを消してLaravelから読み取れるようにする
 */
const versioningRemover = (original) => {
  replaces.forEach(replace => {
    if (original.startsWith(replace.key)) {
      try {
        fs.copyFileSync(path.join(__dirname, 'public', original), path.join(__dirname, 'public', replace.dist));
      } catch (e) {}
    }
  });
};

mix
  .copyDirectory('frontend/dist/spa/css', 'public/css')
  .copyDirectory('frontend/dist/spa/fonts', 'public/fonts')
  .copyDirectory('frontend/dist/spa/icons', 'public/icons')
  .copyDirectory('frontend/dist/spa/js', 'public/js')
  .after(stats => Object.entries(stats.compilation.assets).map(data => versioningRemover(data[0])));
