const path = require("path");

module.exports = {
	entry: "./resources/js/index.js",
	output: {
		path: path.resolve(__dirname, "resources/dist/build"),
		filename: "editor.js",
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
		],
	},
	externals: {
		react: "React",
		"react-dom": "ReactDOM",
	},
};
