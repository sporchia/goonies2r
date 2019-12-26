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
const mix = require("laravel-mix");

/*
const BundleAnalyzerPlugin = require("webpack-bundle-analyzer")
  .BundleAnalyzerPlugin;

mix.webpackConfig({
  plugins: [new BundleAnalyzerPlugin()],
});
// */

mix.webpackConfig({
  module: {
    rules: [
      {
        test: /\.tsx?$/,
        loader: "ts-loader",
        exclude: /node_modules/,
      },
    ],
  },
  resolve: {
    extensions: ["*", ".js", ".jsx", ".vue", ".ts", ".tsx"],
  },
});

mix.options({
  hmrOptions: {
    host: "localhost",
    port: "3032",
  },
});

mix
  .js("resources/js/app.js", "public/js")
  .sass("resources/sass/app.scss", "public/css");
