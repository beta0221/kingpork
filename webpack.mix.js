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

// 合併主要 CSS 檔案 (檢查文件是否存在後再合併)
const mainCssFiles = [
    'public/css/reset.css',
    'public/css/bootstrap/bootstrap.min.css',
    'public/css/_navbar.css',
    'public/css/_footer.css'
].filter(file => {
    const fs = require('fs');
    return fs.existsSync(file);
});

if (mainCssFiles.length > 0) {
    mix.styles(mainCssFiles, 'public/css/app-main.css');
}

// 合併主要 CSS 檔案 (檢查文件是否存在後再合併)
const landingCssFiles = [
    'public/css/Style_landingPage_0320.css',
    'public/css/owl-carousel/owl.carousel.css'
].filter(file => {
    const fs = require('fs');
    return fs.existsSync(file);
});

if (landingCssFiles.length > 0) {
    mix.styles(landingCssFiles, 'public/css/app-landing.css');
}



// 合併主要 JavaScript 檔案
const mainJsFiles = [
    'public/js/jquery/jquery-3.2.1.min.js',
    'public/js/bootstrap/bootstrap.min.js',
    'public/js/prefix-free/prefixfree.dynamic-dom.min.js'
].filter(file => {
    const fs = require('fs');
    return fs.existsSync(file);
});

if (mainJsFiles.length > 0) {
    mix.scripts(mainJsFiles, 'public/js/app-main.js');
}

// 合併 Landing Page JavaScript 檔案
const landingJsFiles = [
    'public/js/owl-carousel/owl.carousel.min.js',
    'public/js/landingPage.js',
    'public/js/lazy-loading.js'
].filter(file => {
    const fs = require('fs');
    return fs.existsSync(file);
});

if (landingJsFiles.length > 0) {
    mix.scripts(landingJsFiles, 'public/js/app-landing.js');
}


// 設定選項
mix.options({
    processCssUrls: false
});

// 版本控制和快取清除
if (mix.inProduction()) {
    mix.version();
}
