/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: "#FFFFFF",
                DarkBlue: "#2E5077",
                MiddelBlue: "#4DA1A9",
                LightBlue: "#79D7BE",
            },
        },
    },
    plugins: [],
};
