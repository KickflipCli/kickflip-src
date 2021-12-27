module.exports = {
    content: [
        'cache/**/*.php',
        'resources/**/*.{js,pcss,php}',
        'source/**/*.{html,md,js,php,vue}',
        'build_*/**/*.html',
    ],
  theme: {
    extend: {},
  },
  plugins: [
      require('@tailwindcss/typography'),
  ],
}
