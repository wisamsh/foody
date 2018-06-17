/**
 * Created by moveosoftware on 5/14/18.
 */
const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
module.exports = {
    optimization: {
        splitChunks: {
            // chunks: 'async',
            minSize: 30000,
            minChunks: 1,
            maxAsyncRequests: 5,
            maxInitialRequests: 3,
            name: true,
            cacheGroups: {
                vendors: {
                    test: /[\\/]node_modules[\\/]/,
                    priority: -10
                },
                default: {
                    minChunks: 1,
                    priority: -20,
                    reuseExistingChunk: true
                }
            }
        }
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            Popper: ['popper.js', 'default'],
            IScroll: 'iscroll'
        }),
        new ExtractTextPlugin({
            filename: (getPath) => {
                return getPath('css/[name].css').replace('css/js', 'css');
            },
            allChunks: true
        })
    ],
    entry: {
        // common : "./resources/pages/common/index",
        // home: "./resources/pages/homepage/index",
        main: "./resources/js/app"
    },
    output: {
        // filename: '[name].js',
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'dist'),
        // publicPath: '/resources'
    },
    mode: 'development',
    module: {
        rules: [
            {
                test: /\.(scss)$/,
                use: [
                    {
                        loader: 'style-loader', // inject CSS to page
                    },
                    {
                        loader: 'css-loader', // translates CSS into CommonJS modules
                    },
                    {
                        loader: 'postcss-loader', // Run post css actions
                        options: {
                            plugins: function () { // post css plugins, can be exported to postcss.config.js
                                return [
                                    require('precss'),
                                    require('autoprefixer')
                                ];
                            }
                        }
                    },
                    {
                        loader: 'sass-loader' // compiles Sass to CSS
                    }
                ]
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf|svg)$/,
                exclude: /node_modules/,
                loader: 'url-loader?limit=102400000000'
            },
            {
                test: /\.(png|jp(e*)g)$/,
                use: [{
                    loader: 'url-loader',
                    options: {
                        limit: 8000, // Convert images < 8kb to base64 strings
                        name: 'images/[hash]-[name].[ext]'
                    }
                }]
            },
            // {
            //     test: /\.(eot|svg|ttf|woff|woff2|otf|svg)$/,
            //     use: [{
            //         loader: 'file-loader',
            //         options: {
            //             name: '[name].[ext]'
            //             // name: '[name].[ext]'
            //         }
            //     }]
            // },
            // { test: /\.woff$/, loader: 'url-loader?limit=6500000000&mimetype=application/font-woff&name=fonts/[name].[ext]' },
            // { test: /\.woff2$/, loader: 'url-loader?limit=6500000000&mimetype=application/font-woff2&name=fonts/[name].[ext]' },
            // {
            //     test: /\.otf$/,
            //     loader: 'url-loader?limit=6500000000&mimetype=application/octet-stream&name=fonts/[name].[ext]'
            // },
            // {
            //     test: /\.ttf$/,
            //     loader: 'url-loader?limit=6500000000&name=fonts/[name].[ext]'
            // },
            // { test: /\.eot$/, loader: 'url-loader?limit=65000000&mimetype=application/vnd.ms-fontobject&name=fonts/[name].[ext]' }

        ]
    },
    watch: true
};