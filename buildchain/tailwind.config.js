module.exports = {
  content: [
    './src/**/*.{vue,js,ts}',
  ],
  safelist: [],
  theme: {

    // Extend the default Tailwind config here
    extend: {
    },

  },

  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
    require('@tailwindcss/line-clamp'),
    require('@tailwindcss/aspect-ratio'),
  ],
};