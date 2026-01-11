import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
// const colores = require('tailwindcss/colors');

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
        // colors: {
        //     'light-blue': colores.lightBlue,
        //     cyan: colores.cyan,
        //     geekBlue: {
        //         50: '#E6F7FF',
        //         100: '#BAE7FF',
        //         200: '#91D5FF',
        //         300: '#69C0FF',
        //         400: '#40A9FF',
        //         500: '#1890FF',
        //         600: '#096DD9',
        //         700: '#0050B3',
        //         800: '#003A8C',
        //         900: '#002766',
        //     },
        //     primary: {
        //         50: '#E6F7FF',
        //         100: '#BAE7FF',
        //         200: '#91D5FF',
        //         300: '#69C0FF',
        //         400: '#40A9FF',
        //         500: '#1890FF',
        //         600: '#096DD9',
        //         700: '#0050B3',
        //         800: '#003A8C',
        //         900: '#002766',
        //     },
        //     blueGray: {
        //         50: '#F8FAFC',
        //     },
        // },
    },

    plugins: [forms],
};
