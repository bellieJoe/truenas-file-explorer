const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
 mix.webpackConfig({
    node: {
      fs: "empty",
      net: "net-browserify"
    //   net: 'empty',
    //   path: false,
    //   os: false
    },
    resolve: {
        alias: {
            "handlebars" : "handlebars/dist/handlebars.js"
        }
    },
});
mix.js('resources/js/app.js', 'public/js')
.js('resources/js/chunk-upload.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ])
    
