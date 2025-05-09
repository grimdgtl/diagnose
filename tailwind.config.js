import forms from '@tailwindcss/forms'; // више не треба
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.vue',
    './resources/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        orange: '#FF5C00',
        black:  '#0a0a0a',
        gray:   '#121212',
        white:  '#ffffff',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
};
