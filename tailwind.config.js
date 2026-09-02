import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "Poppins", ...defaultTheme.fontFamily.sans],
                poppins: ["Poppins", "sans-serif"],
            },
            colors: {
                navy: {
                    50: "#f0f3f9",
                    100: "#dae1f0",
                    200: "#b8c6e3",
                    300: "#8da4d0",
                    400: "#6580ba",
                    500: "#4563a0",
                    600: "#354d85",
                    700: "#2c3f6c",
                    800: "#1e2d4f",
                    900: "#162240",
                    950: "#0d1526",
                },
                "brand-orange": {
                    50: "#fff7ed",
                    100: "#ffedd5",
                    200: "#fed7aa",
                    300: "#fdba74",
                    400: "#fb923c",
                    500: "#f97316",
                    600: "#ea580c",
                    700: "#c2410c",
                    800: "#9a3412",
                    900: "#7c2d12",
                },
            },
        },
    },

    plugins: [forms],
};
