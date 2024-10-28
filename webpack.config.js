/**
 * Webpack configuration file for the plugin.
 */
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    mode: 'production',
    entry: './assets/js/script.js', // Single entry point for both JS and CSS
    output: {
        path: path.resolve(__dirname, 'build'),
        filename: 'script.js', // Outputs only script.js for JavaScript
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: 'babel-loader'
            },
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader'
                ]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'style.css' // Outputs style.css
        })
    ],
    resolve: {
        extensions: ['.js', '.scss']
    }
};
