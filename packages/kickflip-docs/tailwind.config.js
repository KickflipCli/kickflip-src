const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
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
            screens: {
                'sm-max': {'min': '640px', 'max': '767px'},
                'md-max': {'min': '768px', 'max': '1023px'},
                'lg-max': {'min': '1024px', 'max': '1279px'},
                'xl-max': {'min': '1280px', 'max': '1535px'},
            }
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
