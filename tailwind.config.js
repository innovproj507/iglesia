/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './themes/iglesia-base/templates/**/*.php',
        './themes/iglesia-base/src/**/*.js',
        './plugins/**/views/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                // Teal principal (#1a939b)
                brand: {
                    50: '#edf9fa',
                    100: '#d2f0f1',
                    200: '#a9e1e4',
                    300: '#72cbd0',
                    400: '#3aafb6',
                    500: '#1a939b',
                    600: '#167a81',
                    700: '#15636a',
                    800: '#15565a',
                    900: '#123f42',
                    950: '#0a2729',
                },
                // Acento café cálido (#633f2b)
                earth: {
                    50: '#faf6f2',
                    100: '#f1e7dd',
                    200: '#e2cdb8',
                    400: '#b98a66',
                    500: '#9a6b4b',
                    600: '#7d5339',
                    700: '#633f2b',
                    800: '#4b3021',
                    900: '#2f1e15',
                },
            },
            fontFamily: {
                sans: ['Raleway', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                serif: ['"Playfair Display"', 'Georgia', 'serif'],
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(24px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'slow-zoom': {
                    '0%': { transform: 'scale(1.08)' },
                    '100%': { transform: 'scale(1)' },
                },
            },
            animation: {
                'fade-up': 'fade-up .8s ease-out both',
                'slow-zoom': 'slow-zoom 12s ease-out both',
            },
        },
    },
    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
