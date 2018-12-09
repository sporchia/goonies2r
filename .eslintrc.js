// https://eslint.org/docs/user-guide/configuring

module.exports = {
	root: true,
	parserOptions: {
        parser: 'babel-eslint',
        ecmaVersion: 2017,
    },
	env: {
		browser: true,
        es6: true,
    },
	extends: [
		// https://github.com/vuejs/eslint-plugin-vue#priority-a-essential-error-prevention
		// consider switching to `plugin:vue/strongly-recommended` or `plugin:vue/recommended` for stricter rules.
		'plugin:vue/essential',
		// https://github.com/standard/standard/blob/master/docs/RULES-en.md
		'standard',
	],
	// required to lint *.vue files
	plugins: [
		'vue'
	],
	// add your custom rules here
	rules: {
		'comma-dangle': ['error', 'always-multiline'],
		// allow async-await
		'generator-star-spacing': 'off',
		//'indent': ['error', 'tab', {'SwitchCase': 1}],
		// allow debugger during development
		'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    'handle-callback-err': 'off',
		'semi': ['error', 'always'],
		'space-before-function-paren': ['error', 'never'],
	},
};
