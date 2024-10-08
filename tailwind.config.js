import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'purple-5': "#0f0529",
                'purple-4': "#4a2574",
                'purple-3': "#7338a0",
                'purple-2': "#924dbf",
                'purple-1': "#9e72c3",
                "purple-05": "#cdb4e1",
                "purple-025": "#f0e9f5",
                "purple-015": "#fdf9ff"
            }
        },
    },

    plugins: [forms],
};
