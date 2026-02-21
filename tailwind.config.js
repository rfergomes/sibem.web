/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
            colors: {
                // SIBEM Brand Colors based on the request "Premium/Modern"
                ccb: {
                    dark: '#111827', // Header Color
                    blue: '#033D60', 
                    green: '#22c55e', 
                }
            }
        },
    },
    plugins: [],
};
