const nodePaths = ['node/**/*.js'];

module.exports = {
	parserOptions: {
		ecmaVersion: 2018,
	},
	overrides: [
		{
			files: ['*.js'],
			excludedFiles: nodePaths,
			...require('./.eslintrc.php.js'),
		},
		{
			files: nodePaths,
			...require('./.eslintrc.node.js'),
		},
	]
};
