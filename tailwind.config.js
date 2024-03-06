import defaultTheme from 'tailwindcss/defaultTheme';
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/css/main.css',
        './resources/views/**',
        './node_modules/flowbite/**/*.js'
    ],
    theme: {
        fontFamily: {
            sans: ['Graphik', 'sans-serif'],
            serif: ['Merriweather', 'serif','Figtree', ...defaultTheme.fontFamily.sans],
        },
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('flowbite/plugin')
    ],
    darkMode: 'class',
}