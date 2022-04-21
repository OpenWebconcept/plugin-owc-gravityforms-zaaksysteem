const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const path = require("path");

module.exports = {
	entry: { 
        "editor": "./resources/js/index.js",
        "zaak-styles": "./resources/scss/style.scss"
    },
	output: {
		path: path.resolve(__dirname, "resources/dist/build"),
	},
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				loader: "babel-loader",
				exclude: "/node_modules/",
				options: {
					presets: ["@babel/preset-react", "@babel/preset-env"],
				},
			},
            {
                test: /.s?css$/,
                use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
            },
		],
	},
    optimization: {
        minimizer: [
            new CssMinimizerPlugin(),
        ],
    },
	externals: {
		react: "React",
		"react-dom": "ReactDOM",
	},
    plugins: [new MiniCssExtractPlugin()],
};
