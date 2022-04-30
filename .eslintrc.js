module.exports = {
  env: {
    browser: true,
    es2021: true,
    jquery: true
  },
  extends: [
    'standard',
    'plugin:vue/essential'
  ],
  parserOptions: {
    ecmaVersion: 'latest',
    sourceType: 'module'
  },
  plugins: [
    'vue',
    'jquery'
  ],
  rules: {
    semi: ['error', 'always'],
    eqeqeq: 'off',
    camelcase: 'off',
    'space-before-function-paren': 'off',
    'no-new': 'off',
    'vue/require-prop-types': 'off',
    'vue/no-mutating-props': 'off'
  }
};
