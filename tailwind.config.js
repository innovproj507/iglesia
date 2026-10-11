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
                brand: {
                    50: '#f7ecec',
                    100: '#eed6d9',
                    300: '#c07684',
                    500: '#9a3346',
                    700: '#7A1F2B',
                    800: '#5c1720',
                    900: '#3d0f15',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
