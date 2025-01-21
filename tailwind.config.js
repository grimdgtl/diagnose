import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */

export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    // ili bilo koje putanje koje koristiš...
  ],
  theme: {
    extend: {
      colors: {
        // Možeš definisati custom boje prema svojim varijablama
        black: "#0a0a0a",
        orange: "#FF5C00",
        white: "#ffffff",
        gray: "#121212",
      },
      fontFamily: {
        'red-hat': ['"Red Hat Display"', 'sans-serif'],
      },
    },
  },
  plugins: [
    //require('@skeletonlabs/skeleton/tailwind/skeleton.cjs'),
    ],
};
