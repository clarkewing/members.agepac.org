const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/invoice.scss', 'public/css')
    .copy('node_modules/tributejs/dist/tribute.css', 'public/css/vendor')
    .browserSync({
        proxy: 'agepac.test',
        browser: 'google chrome'
    });
