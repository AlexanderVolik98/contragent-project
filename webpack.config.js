const Encore = require('@symfony/webpack-encore');

Encore
    .configureRuntimeEnvironment('development')
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .enableVueLoader()
    .enableSingleRuntimeChunk()
    .addStyleEntry('styles', './assets/styles/app.css') // обработка CSS
    .addEntry('app', './assets/src/app.js')
    .enableSourceMaps(!Encore.isProduction())  // Для отладки в dev-режиме
;

module.exports = Encore.getWebpackConfig();
