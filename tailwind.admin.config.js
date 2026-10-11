/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './core/Admin/Views/**/*.php',
        './core/Install/Views/**/*.php',
        './plugins/**/views/admin/**/*.php',
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
        },
    },
    plugins: [require('@tailwindcss/forms')],
};
