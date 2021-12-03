const mix = require('laravel-mix');
const path = require("path");
const tailwindcss = require('tailwindcss');
const postcssAdvancedVariables = require('@knagis/postcss-advanced-variables');

mix.disableSuccessNotifications();
mix.setPublicPath('source/assets/build');

mix.js('resources/js/main.js', 'js')
    .postCss('resources/postCss/main.pcss', 'css/main.css')
    .options({
        processCssUrls: false,
        postCss: [
            require('postcss-omit-import-tilde'),
            require('postcss-import'),
            postcssAdvancedVariables(),
            tailwindcss('./tailwind.config.js'),
            require('postcss-nested'),
            require('postcss-custom-selectors')(),
            require('autoprefixer'),
    ]})
    .sourceMaps()
    .version();
