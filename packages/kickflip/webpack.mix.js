const mix = require('laravel-mix');
const path = require("path");

mix.disableSuccessNotifications();
mix.setPublicPath('source/assets/build');

mix.js('resources/js/main.js', 'js')
    .postCss('resources/postCss/main.pcss', 'css/main.css')
    .options({
        processCssUrls: false,
        postCss: [
            require('tailwindcss')('./tailwind.config.js'),
    ]})
    .sourceMaps()
    .version();
