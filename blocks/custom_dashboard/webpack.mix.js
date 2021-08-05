const dashboard = require('webpack-dashboard/plugin');
const mix = require('laravel-mix');

mix.setResourceRoot('/blocks/custom_dashboard/');

mix.react('js/index.js', 'dist/js/app.js');

mix.sass('scss/style.scss', 'dist/css')
    .options({processCssUrls: false});

mix.webpackConfig({
    plugins: [new dashboard({port: 9222})]
});

mix.babelConfig(
    {
        presets: [
            "@babel/preset-env",
            "@babel/preset-react",
            ['@emotion/babel-preset-css-prop', {}]
        ],
        plugins: [
            '@babel/plugin-syntax-dynamic-import',
            ['@babel/plugin-proposal-class-properties', {"loose": true}],
            ['emotion', {}]
        ]
    }
);

// Disable system notifications.
mix.disableNotifications();

// Disable generated manifest file.
Mix.manifest.refresh = _ => void 0;
