/**
 * Created by moveosoftware on 5/14/18.
 */
const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
var HashPlugin = require('hash-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");
// the path(s) that should be cleaned
let pathsToClean = [
    'dist'
];

// the clean options to use
let cleanOptions = {
    root: path.resolve(__dirname, ''),
    verbose: true,
    dry: false
};

module.exports = env => {


    return {
        optimization: {
            splitChunks: {
                // chunks: 'async',
                minSize: 20,
                minChunks: 1,
                maxAsyncRequests: 1,
                maxInitialRequests:1,
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
            },
            minimizer:[
                new UglifyJsPlugin({
                    cache: true,
                    parallel: true,
                    sourceMap: false // set to true if you want JS source maps
                }),
                new OptimizeCSSAssetsPlugin({})
            ]
        },
        plugins: [
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
                'window.jQuery': 'jquery',
                'window.$': 'jquery',
                Popper: ['popper.js', 'default'],
                IScroll: 'iscroll'
            }),
            new ExtractTextPlugin({
                filename: (getPath) => {
                    return getPath('css/[name].css').replace('css/js', 'css');
                },
                allChunks: true
            }),
            new HashPlugin({path: './build', fileName: 'version-hash.txt'}),
            new CleanWebpackPlugin(pathsToClean, cleanOptions),
            new CompressionPlugin()
        ],
        entry: {
            admin: ["./resources/js/admin","./resources/js/page-load.entry"],
            author: ["./resources/js/author.entry","./resources/js/page-load.entry"],
            campaign: ["./resources/js/campaign.entry","./resources/js/page-load.entry"],
            categories: ["./resources/js/categories.entry","./resources/js/page-load.entry"],
            centered_content: ["./resources/js/centered-content.entry","./resources/js/page-load.entry"],
            channel: ["./resources/js/channel.entry","./resources/js/page-load.entry"],
            homepage_js: ["./resources/js/homepage.entry","./resources/js/page-load.entry"],
            items: ["./resources/js/items.entry","./resources/js/page-load.entry"],
            login: ["./resources/js/login.entry","./resources/js/page-load.entry"],
            playlist: ["./resources/js/playlist.entry","./resources/js/page-load.entry"],
            post: ["./resources/js/post.entry","./resources/js/page-load.entry"],
            profile: ["./resources/js/profile.entry","./resources/js/page-load.entry"],
            register: ["./resources/js/register.entry","./resources/js/page-load.entry"],
            tag: ["./resources/js/tag.entry","./resources/js/page-load.entry"],
            team: ["./resources/js/team.entry","./resources/js/page-load.entry"],
            style: ["./resources/sass/app.scss"],
            homepage: ["./resources/sass/homepage_app.scss"],
        },
        output: {
            // filename: '[name].js',
            filename: '[name].[hash].js',
            path: path.resolve(__dirname, 'dist'),
            publicPath: '/app/themes/Foody/dist/'
            // publicPath: path.resolve(__dirname, 'resources')
        },
        mode: 'development',
        module: {
            rules: [
                {
                    test: /\.css$/,
                    loaders: ["style-loader", "css-loader"]
                },
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
                    test: /\.(png|jp(e*)g|gif)$/,
                    use: [{
                        loader: 'url-loader',
                        options: {
                            limit: 8000, // Convert images < 8kb to base64 strings
                            name: 'images/[hash]-[name].[ext]'
                        }
                    }]
                },
                {
                    type: 'javascript/auto',
                    test: /\.(json)/,
                    exclude: /(node_modules)/,
                    use: [{
                        loader: 'file-loader',
                        options: {name: '[name].[ext]'},
                    }]
                },
                {
                    test: /\.js$/, //Regular expression
                    exclude: /(node_modules|bower_components)/,//excluded node_modules
                    use: {
                        loader: "babel-loader",
                        options: {
                            presets: ["@babel/preset-env"]  //Preset used for env setup
                        }
                    }
                },
                {
                    test: /\.js$/,
                    use: {
                        loader: "babel-loader",
                        options: {
                            plugins: [
                                "@babel/plugin-syntax-dynamic-import"
                            ]
                        }
                    }
                }
            ]
        },
        watch: false,
        node: {
            fs: 'empty'
        }
    };
};