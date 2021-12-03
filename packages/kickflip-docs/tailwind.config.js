const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    purge: {
        content: [
            'cache/**/*.php',
            'resources/**/*.js',
            'resources/**/*.pcss',
            'resources/**/*.php',
            'source/**/*.html',
            'source/**/*.md',
            'source/**/*.js',
            'source/**/*.php',
            'source/**/*.vue',
        ],
        options: {
            whitelist: [
                /language/,
                /hljs/,
                /algolia/,
            ],
        },
    },
    theme: {
        extend: {
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        color: theme('colors.coolGray.900'),
                        pre: null,
                        'pre code': null,
                        'pre code.hljs': {
                            padding: '0.5rem'
                        },
                    },
                },
            }),
        },
    },
    variants: {
        borderRadius: ['responsive', 'focus'],
        borderWidth: ['responsive', 'active', 'focus'],
        width: ['responsive', 'focus']
    },
    plugins: [
        require('@tailwindcss/typography'),
    ]
}
