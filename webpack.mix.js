const mix = require('laravel-mix');
const SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin');

// Определяем важные пути
const resources_path = './wp-content/themes/classy/';

// Куда копировать ресурсы из CSS
mix.setPublicPath(resources_path + 'dist');

// Автоподмена пути к ресурсам в cSS
mix.setResourceRoot(resources_path + 'dist');

// Для маски - @import "blocks/**/*.scss"
mix.webpackConfig({module: {rules: [{test: /\.scss$/, loader: 'import-glob-loader'},]}});

mix.options({
    processCssUrls: false,
});

mix.webpackConfig({
    plugins: [
        new SVGSpritemapPlugin(resources_path + 'img/svg/*.svg', {
            output: {
                filename: 'img/sprite.svg',
                svgo: true,
                chunk: {
                    name: 'spritemap',
                    keep: true,
                },
            },

            sprite: {
                prefix: false,
                generate: {
                    title: false,
                    symbol: true,
                    use: false,
                    view: false
                }
            },
        }),
    ]
});

// fix excluding
mix.webpackConfig({
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /(bower_components)/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: mix.config.babel(),
                    }
                ]
            }
        ]
    }
});

// JS
mix.js(resources_path + 'assets/js/index.js', resources_path + 'dist').autoload({
    jquery: ['$', 'window.jQuery', 'jQuery']
})

// SASS
.sass(resources_path + 'assets/sass/main.scss', resources_path + 'dist/main.css')
//.sass(resources_path + 'sass/print.scss', 'dist')

// Generate sourceMaps
.sourceMaps(true, 'source-map')

// Add hash version to file {{ mix('/css/app.css') }}
.copy(resources_path + 'assets/img', resources_path + 'dist/img', false)
.copy(resources_path + 'assets/fonts', resources_path + 'dist/fonts', false)
.copy(resources_path + 'assets/js/libs/fontawesome/webfonts', resources_path + 'dist/fonts/fontawesome')
.version();

if (mix.inProduction()) {
    //mix.version();
}
