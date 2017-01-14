var debug = process.env.NODE_ENV !== "production";
var webpack = require('webpack');
var path = require('path');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var CopyWebpackPlugin = require('copy-webpack-plugin');


module.exports = {
  // context: path.join(__dirname, "src"),
  entry: {
    // vendor: [
    //   "react",
    //   "react-dom",
    //   "react-tap-event-plugin",
    //   "whatwg-fetch", // AJAX fetch polyfill - https://github.com/github/fetch
    //   "material-ui/styles/MuiThemeProvider"
    // ],
    bundle: "./src/index.js"
  },
  output: {
    path: path.resolve('./build'),
    publicPath: '/',
    filename: "[name].js"
  },
  devtool: debug ? "inline-sourcemap" : null,
  module: {
    loaders: [
      {
        test: /\.jsx?$/,
        exclude: /(node_modules|vendor|repositories|venv)/,
        loader: 'babel', //-loader (optional)
        query: {
          // cacheDirectory: true,
          presets: ['es2015', 'stage-0', 'react'],
          plugins: ['transform-decorators-legacy', 'react-html-attrs', 'transform-class-properties'],
        }
      },
      // {
      //   // make all files ending in .json5 use the `json5-loader`
      //   test: /\.json5$/,
      //   exclude: /(node_modules|vendor|repositories|venv)/,
      //   loader: 'json5-loader'
      // },
      // fonts and svg
      // { test: /\.woff(\?v=\d+\.\d+\.\d+)?$/, loader: "url?limit=10000&mimetype=application/font-woff" },
      // { test: /\.woff2(\?v=\d+\.\d+\.\d+)?$/, loader: "url?limit=10000&mimetype=application/font-woff" },
      // { test: /\.ttf(\?v=\d+\.\d+\.\d+)?$/, loader: "url?limit=10000&mimetype=application/octet-stream" },
      // { test: /\.eot(\?v=\d+\.\d+\.\d+)?$/, loader: "file" },
      // { test: /\.svg(\?v=\d+\.\d+\.\d+)?$/, loader: "url?limit=10000&mimetype=image/svg+xml" },
      // images
      {
        test: /\.(ico|jpe?g|png|gif)$/,
        loader: "file"
      },
      {
        test: /\.scss$/,
        exclude: [ /vendor/, /node_modules/, /repositories/, /venv/ ],
        loader: ExtractTextPlugin.extract("style", "css?sourceMap!postcss!sass?sourceMap&outputStyle=expanded")
      }
    ]
  },
  resolve: {
    extensions: ['', '.jsx', '.js', '.json', '.scss', '.css'],
    modulesDirectories: [
      'node_modules',
      'src/Components'
    ]
  },
  plugins: debug ?
  //DEV
  [
    new ExtractTextPlugin('style.css', {
      allChunks: true
    }),
    new CopyWebpackPlugin([
      {from: './data', to: './data'}
    ]),
    new webpack.DefinePlugin({
        'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development')
    })
  ]
  :
  [
    new ExtractTextPlugin('style.css', {
      allChunks: true
    }),
    new CopyWebpackPlugin([
      {from: './data', to: './data'}
    ]),
    new webpack.optimize.DedupePlugin(),
    new webpack.optimize.OccurenceOrderPlugin(),
    new webpack.DefinePlugin({
        'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development')
    }),
  	new webpack.optimize.UglifyJsPlugin({
        // mangle: false,
        compress: {
            warnings: false
        }
     })
  ]
};
