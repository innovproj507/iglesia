/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './core/Admin/Views/**/*.php',
        './core/Install/Views/**/*.php',
        './plugins/**/views/admin/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
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
                earth: {
                    50: '#faf6f2',
                    100: '#f1e7dd',
                    200: '#e2cdb8',
                    400: '#b98a66',
                    500: '#9a6b4b',
                    600: '#7d5339',
                    700: '#633f2b',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                serif: ['"Playfair Display"', 'Georgia', 'serif'],
            },
            boxShadow: {
                soft: '0 1px 2px rgba(16,24,40,.04), 0 4px 16px -4px rgba(16,24,40,.06)',
            },
        },
    },
    plugins: [require('@tailwindcss/forms')],
};
