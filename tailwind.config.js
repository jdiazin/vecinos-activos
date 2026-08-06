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
        brand: {
          primary: '#7c3aed', // Violeta
          secondary: '#db2777', // Fucsia
        },
      },
    },
  },
  plugins: [],
}